pipeline {
    agent any

    environment {
        IMAGE = 'boozy7/cucikilat'
        TAG = "${env.BUILD_NUMBER}"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        // Tantangan DevOps K06: Stage Lint sebelum Test (Quality Gate Pint di dalam container)
        stage('Lint') {
            steps {
                bat 'docker build --target lint -t %IMAGE%:lint .'
            }
        }

        // Automated Testing Quality Gate (Semua unit & feature test wajib hijau)
        stage('Test') {
            steps {
                bat 'docker build --target test -t %IMAGE%:test .'
            }
        }

        // Build Production Docker Image dengan 2 tag (BUILD_NUMBER & latest)
        stage('Build Image') {
            steps {
                bat 'docker build --target runner -t %IMAGE%:%TAG% -t %IMAGE%:latest .'
            }
        }

        // Push Image ke Docker Hub menggunakan Jenkins Credentials (tanpa token di kode)
        stage('Push Image') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub-credentials',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    bat 'powershell -NoProfile -Command "$env:DOCKER_PASS | docker login -u $env:DOCKER_USER --password-stdin"'
                    bat 'docker push %IMAGE%:%TAG%'
                    bat 'docker push %IMAGE%:latest'
                }
            }
        }

        // Deploy dengan Docker Compose menggunakan .env dari Jenkins Secret File
        stage('Deploy') {
            steps {
                withCredentials([file(credentialsId: 'cucikilat-env', variable: 'ENV_FILE')]) {
                    bat '''
                        copy /Y "%ENV_FILE%" .env >nul
                        docker compose pull app
                        docker compose up -d
                    '''
                }
            }
        }

        // Smoke Test: Memanggil endpoint /health (pipeline gagal jika respons bukan HTTP 200)
        stage('Smoke Test') {
            steps {
                bat 'powershell -NoProfile -Command "Start-Sleep -Seconds 15"'
                bat 'curl.exe -fsS http://localhost:8000/health'
            }
        }
    }

    post {
        success {
            echo "Pipeline CuciKilat (K06) SUKSES: Image ${env.IMAGE}:${env.TAG} ter-deploy dan /health lulus (HTTP 200)."
        }
        failure {
            echo "Pipeline CuciKilat (K06) GAGAL: Periksa stage yang merah pada Stage View."
        }
    }
}
