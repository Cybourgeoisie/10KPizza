# Using Docker

# Ways to debug:
	docker logs [container]


# Development Builds
	# At root (/)
	docker build -t 10kpizza-dev .

	# At /sql/
	docker build -t 10kpizza-db-dev .


# Production Builds
	# At root (/)
	docker build -f Dockerfile-production -t 10kpizza-prod .


# Development Run

# Instructions: Using Docker Compose
	# Make sure that the data persistence container is running
	docker run -i --name 10kpizza-db-data 10kpizza-db-dev /bin/echo "PostgreSQL data container"

	# Run docker compose to set up the database container and web-accessible container
	docker-compose up -d
	docker-compose down
