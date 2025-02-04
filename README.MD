# Blogging Platform API
In this project you are required to develop a REST API for user authentication and authorization using JWT and CRUD Blog operations using PDO. 
## Goals
The skills you will learn from this project include:

- User authentication
- Schema design and databse
- Restful API design
- JWT (JSON Web Token)
- CRUD operations with PDO
- Error handling
- form validation

## Requirements
You are required to Rest API for user authentication and CRUD blog authorization using JWT.
- User registration to create a new user
- Login endpoint to authenticate the user and generate a token
- Implementation of Create, Read, Update, Delete operations Blog with PDO
- Implementation of error handling and form validation
- Use a database to store the users data (for begginer choose MySQL)
### User Registration
Register a new user account
```json 
POST /api/v1/auth/register
{
    "username": "text",
    "email": "text@gmail.com",
    "password_hash": "text1@gmail.com"
}
```
### User Login
Authentication users login
```json 
POST /api/v1/auth/login
{
    "email": "text@gmail.com",
    "password_hash": "text1@gmail.com"
}
```
This is will be generated JWT token 
```json 
{
    "message": "Login successfully",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJqb2tvLXBvc3RtYW4iLCJleHAiOjE3MzgyMjk2MDB9.JCdVSt3A-X5aONTkHGFWwM5PWzDPkIXTAh0kzBMmjZc",
        "expiry": "2025-01-30T09:33:20+00:00"
    }
}
```
### CRUD Blog operations
Read all blog post
```json 
GET /api/v1/blog 
Response:
{
    "message": "Successfully Get All Blog",
    "data": {
        "blog": [
            {
                "post_id": 1,
                "title": "Data post with auth by oop coonsept",
                "content": "This is the content of the article. data data data",
                "author_id": 2,
                "published_at": "2025-01-30 03:55:20"
            },
            {
                "post_id": 2,
                "title": "Data post with auth oke by oop coonsept",
                "content": "This is the content of the article. data data data",
                "author_id": 2,
                "published_at": "2025-01-30 03:55:20"
            },
        ],
    }
}
```
Read blog post based on ID
```json 
GET /api/v1/blog/2 
Response:
{
    "message": "Successfully Get Blog Based Id",
    "data": {
        "blog": {
            "post_id": 2,
            "title": "Data post with auth oke by oop coonsept",
            "content": "This is the content of the article. data data data",
            "author_id": 2,
            "published_at": "2025-01-30 03:55:20"
        }
    }
}
```
Create a new blog post
```json 
POST /api/v1/blog 
Bearer Token: 
eyJ0eXAiOiJK.eyJpZCI6MTA.Pq4spR_q_sS5I
Input:
{
  "title": "oke",
  "content": "oke"
}
Response:
{
    "message": "Successfully Create Blog Post",
    "data": {
        "blog": {
            "id": "6",
            "title": "oke",
            "content": "oke",
            "author_id": 10,
            "published_at": "2025-02-01 07:00:17"
        }
    }
}
```
Update a blog post based on id
```json 
PUT /api/v1/blog/6 
Bearer Token: 
eyJ0eXAiOiJK.eyJpZCI6MTA.Pq4spR_q_sS5I
Input:
{
  "title": "Upo",
  "content": "Lorem ipsum dolor sit amet"
}
Response:
{
    "message": "Successfully Update Blog Post",
    "data": {
        "blog": {
            "id": 6,
            "title": "Upo",
            "content": "Lorem ipsum dolor sit amet",
            "author_id": 10,
            "published_at": "2025-02-01 07:01:13"
        }
    }
}
```
Delete a blog post based on id
```json 
DELETE /api/v1/blog/6 
Bearer Token: 
eyJ0eXAiOiJK.eyJpZCI6MTA.Pq4spR_q_sS5I
Response:
{
    "message": "Deleted successfully"
}
```
### Error-Handling and Form-validation
Validating Users Input
```json 
POST /api/v1/auth/register
{
    "errors": {
        "email": "email is required",
        "username": "max:50 is required",
        "password_hash": "max:255 is required"
    }
}
```
```json 
POST /api/v1/blog || PUT /api/v1/blog/2
{
    "errors": {
        "title": "min of 3 characters is required",
        "content": "max:255 is required"
    }
}
```
This project will help you understand how to implement REST API with PHP using OOP and JWT. 
