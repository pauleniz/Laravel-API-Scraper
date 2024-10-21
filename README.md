
# Project Title

**Laravel Web Scraping API**

This project is a Laravel-based backend for scraping web content based on user-supplied URLs and CSS selectors. It provides a set of RESTful endpoints to create, retrieve, and delete scraping jobs. Redis is used for job management, and jobs are processed asynchronously using Laravel Queues.

## Prerequisites

Ensure you have the following installed:
- **Docker**

## Setup Instructions

Follow these steps to get the project up and running:

### 1. Clone the Repository

```bash
git clone https://github.com/pauleniz/homework.git
cd your-project
```


### 2. Set Up the .env File

Copy the `.env.example` file to `.env` and configure your environment variables, especially for the database and Redis connections:

```bash
cp .env.example .env
```

### 3. Install Dependencies

To install all the necessary PHP and JavaScript dependencies, run the following command:

```bash
docker run --rm -v "${PWD}:/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
```

### 4. Set up project
If using Windows go to WSL.

Run the following command to set up the rest of the project:

```bash
make start
```

### 5. Run migrations and seed the database
Run the following command to run migrations and seed the database:

```bash
make migrate-seed
```

### 6. Running the Queue Worker

Since jobs are processed asynchronously, make sure the queue worker is running:

```bash
make queue
```

### 7. Running Tests

You can run the tests to ensure everything is working properly:

```bash
make test
```

## API Documentation

This project uses **Swagger** for API documentation, allowing you to interact with the API endpoints in real-time.

### Accessing the Swagger Documentation

Once the project is up and running, you can access the Swagger UI to explore and test the API.

- **URL**: [http://localhost/api/v1/documentation](http://localhost/api/v1/documentation)

### Interactive Features
- **Try it out**: You can send requests to the API directly from the Swagger UI by clicking the "Try it out" button for any endpoint.
- **Authorization**: Use the **Authorize** button to provide your API token (in the format `Bearer <your_token_here>`) for authenticated endpoints.


