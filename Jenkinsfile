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

        // Tantangan DevOps K06: Stage Lint sebelum Test (Quality Gate Pint)
        stage('Lint') {
            steps {
                sh 'docker build --target lint -t $IMAGE:lint .'
            }
        }

        // Automated Testing Quality Gate (Semua test wajib hijau)
        stage('Test') {
            steps {
                sh 'docker build --target test -t $IMAGE:test .'
            }
        }

        // Build Production Docker Image dengan 2 tag (BUILD_NUMBER & latest)
        stage('Build Image') {
            steps {
                sh 'docker build --target runner -t $IMAGE:$TAG -t $IMAGE:latest .'
            }
        }

        // Push Image ke Docker Hub menggunakan Jenkins Credentials
        stage('Push Image') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub-credentials',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    sh 'echo $DOCKER_PASS | docker login -u $DOCKER_USER --password-stdin'
                    sh 'docker push $IMAGE:$TAG'
                    sh 'docker push $IMAGE:latest'
                }
            }
        }

        // Deploy dengan Docker Compose
        stage('Deploy') {
            steps {
                sh 'docker compose pull app'
                sh 'docker compose up -d'
            }
        }

        // Smoke Test: Memanggil endpoint /health (wajib HTTP 200)
        stage('Smoke Test') {
            steps {
                sh 'sleep 15'
                sh 'curl -fsS http://localhost:8000/health'
            }
        }
    }

    post {
        success {
            echo "Pipeline CuciKilat (K06) SUKSES: Aplikasi berhasil dideploy dan health check lulus."
        }
        failure {
            echo "Pipeline CuciKilat (K06) GAGAL: Cek stage yang merah pada Stage View."
        }
    }
}
