# Demo Symfony Project

## Overview
This is a demonstration project developed using RESTful architecture style.
The application is currently deployed and running on AWS cloud infrastructure.

## Deployment
The application is hosted on AWS with the following setup:
- EC2 instance for application hosting
- RDS for MSSQL database
- S3 for static asset storage
- Route 53 for DNS management
- ALB for load balancer

## Tech Stack

### Backend
- Symphony Framework
- Microsoft SQL Server (MSSQL)

### Frontend
- Angular(By Restful API)
- JavaScript
- HTML5
- CSS3

## RESTful API
This project follows RESTful architectural style and provides standard HTTP interfaces.

## System Requirements
- PHP 8.3+
- MSSQL Server 2016+
- Symfony 6
- Angular CLI


# Course Selection System RESTful API Documentation
---
## Overview
RESTful API endpoints for managing courses in the system. Handles basic CRUD operations for course management.

## Base URL
`/api/courses`

## Endpoints

### GET `/`
Lists all courses in the system.

### GET `/{id}`
Retrieves a specific course by ID.

### POST `/`
Creates a new course. Required fields:
- courseName
- courseCode
- description
- department
- credits

### PUT `/{id}`
Updates an existing course. All fields are optional:
- courseName
- courseCode
- description
- department
- credits

### DELETE `/{id}`
Removes a course from the system.

## Authentication
- Default professor (ID: 1) is assigned if no user is authenticated
- All endpoints are publicly accessible

## Response Format
All responses are in JSON format with appropriate HTTP status codes:
- 200: Success
- 201: Created
- 404: Not Found
- 500: Server Error