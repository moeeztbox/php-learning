# Week 2 - Day 4

## Authentication Security Concepts

This document summarizes common web authentication attacks, compares session-based and token-based authentication, and explains when each authentication style should be used.

---

# 1. CSRF (Cross-Site Request Forgery)

## What is CSRF?

CSRF is an attack where a malicious website tricks a user's browser into sending an unwanted request to another website where the user is already authenticated.

The attacker does not steal the user's password or session. Instead, they take advantage of the browser automatically sending authentication cookies with every request.

### Vulnerable Authentication

- Session-based authentication ✅
- JWT stored inside cookies ✅
- JWT stored in Local Storage ❌ (Traditional CSRF is generally prevented because the browser does not automatically send the token.)

### Prevention

- CSRF Tokens
- SameSite Cookies
- Origin/Referer Validation

---

# 2. XSS (Cross-Site Scripting)

## What is XSS?

XSS is an attack where an attacker injects malicious JavaScript into a web page. When another user visits that page, the malicious script executes inside the user's browser.

The attacker may steal sensitive information such as authentication tokens, cookies (if accessible), or perform actions on behalf of the user.

### Vulnerable Authentication

- JWT stored in Local Storage ✅
- Session cookies without HttpOnly ✅
- HttpOnly cookies provide protection against JavaScript access.

### Prevention

- Validate user input
- Escape HTML output
- Sanitize user-generated content
- Use Content Security Policy (CSP)
- Store authentication cookies as HttpOnly

---

# 3. Session Hijacking

## What is Session Hijacking?

Session hijacking is an attack where an attacker obtains a valid session identifier and uses it to impersonate an authenticated user.

The attacker may obtain the session ID through XSS, network interception (without HTTPS), or session fixation.

### Vulnerable Authentication

- Session-based authentication ✅
- JWT can also be stolen if stored insecurely.

### Prevention

- Always use HTTPS
- Regenerate Session IDs after login
- Use Secure and HttpOnly cookies
- Set appropriate session expiration
- Protect against XSS

---

# Attack Comparison

| Attack | What it Does | Mainly Targets | Prevention |
|---------|--------------|----------------|------------|
| CSRF | Tricks the browser into sending unwanted authenticated requests | Session Authentication | CSRF Tokens, SameSite Cookies |
| XSS | Executes malicious JavaScript inside the browser | Sessions and JWT | Input Validation, Output Escaping, CSP |
| Session Hijacking | Steals an authenticated user's session or token | Sessions and JWT | HTTPS, Secure Cookies, Session Regeneration |

---

# Session-Based Authentication

## Advantages

- Easy to implement
- Sessions remain on the server
- Easy logout by destroying the session
- Suitable for traditional web applications

## Disadvantages

- Requires server-side session storage
- Harder to scale across multiple servers without shared session storage
- Vulnerable to CSRF if not protected

---

# Token-Based Authentication (JWT)

## Advantages

- Stateless authentication
- Easy to scale across multiple servers
- Well suited for REST APIs and mobile applications
- No server-side session storage required

## Disadvantages

- Logout is more difficult because the server does not track issued tokens
- Tokens remain valid until expiration unless additional revocation mechanisms are implemented
- JWT stored in Local Storage can be stolen through XSS

---

# Session vs JWT

| Feature | Session-Based | JWT-Based |
|----------|--------------|----------|
| Authentication State | Stored on Server | Stored inside Token |
| Server Storage | Required | Not Required |
| Scalability | Moderate | Excellent |
| Logout | Immediate | Usually waits for token expiration or requires revocation |
| CSRF Risk | High | Low when using Authorization Header |
| XSS Risk | Low with HttpOnly cookies | High if stored in Local Storage |
| Best Use Case | Traditional Web Applications | REST APIs, Mobile Apps, SPAs |

---

# When to Use Session Authentication

Use session-based authentication when:

- Building traditional server-rendered web applications.
- The application mainly runs in a browser.
- Immediate logout is required.
- Server-side session management is acceptable.

---

# When to Use JWT Authentication

Use JWT authentication when:

- Building REST APIs.
- Supporting mobile applications.
- Developing Single Page Applications (SPA).
- Building distributed or microservice architectures.
- Stateless authentication is preferred.

---

# Conclusion

Both session-based and token-based authentication are secure when implemented correctly.

Session authentication is simple and ideal for traditional web applications, while JWT authentication provides better scalability and flexibility for APIs and modern applications.

The choice between them depends on the application's architecture, security requirements, and deployment environment rather than one method being universally better than the other.

------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Week 2 - Day 5

# OAuth2 Third-Party Authentication Concepts

## What is OAuth?

OAuth (Open Authorization) is an authorization protocol that allows an application to access a user's information from another service without sharing the user's password.

It is commonly used for third-party login systems such as:

- Sign in with Google
- Sign in with Facebook
- Sign in with GitHub

The main purpose of OAuth is to allow secure access by using tokens instead of sharing user credentials.

---

# What is "Sign in with Google"?

"Sign in with Google" is an implementation of OAuth where an application uses Google's authentication system to verify a user's identity.

When a user clicks "Sign in with Google":

- The application does not receive the user's Google password.
- Google handles the authentication process.
- Google provides an authorization code/token to the application.
- The application uses that token to get approved user information like name and email.

This keeps the user's Google account credentials secure.

---

# OAuth2 Authorization Code Flow


---

# OAuth Flow Explanation

1. The user clicks the **Sign in with Google** button.

2. The application redirects the user to Google's authentication page.

3. The user logs in and gives permission to share their information.

4. Google sends an authorization code back to the application.

5. The application sends this authorization code to Google to exchange it for an access token.

6. Google verifies the code and returns an access token.

7. The application uses the access token to request the user's approved information.

8. The application creates its own session or JWT and logs the user into the system.

---

# Important Point

The user's password is never shared with the application.

Google handles authentication, while the application only receives limited information through tokens.

---

# Benefits of OAuth

- Users do not need to create a separate password for every application.
- Applications never store third-party passwords.
- Users can control and revoke application access.
- Authentication is handled by trusted providers.

---

# Conclusion

OAuth2 provides a secure way to implement third-party authentication. Features like "Sign in with Google" use the OAuth2 authorization code flow to verify users while keeping their passwords protected.

------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Week 3 - Day 4

## IDOR Vulnerability Fix

### Bug Description

The vulnerable endpoint allowed users to access or modify resources by directly changing the resource ID in the request without verifying ownership or permission. For example, a user could send another user's task ID and attempt to update that task.

### Why It Was Dangerous

This was a security risk because authenticated users could potentially modify data that did not belong to them. Being logged in does not automatically mean the user has permission to access every resource. This could lead to unauthorized data changes and privacy issues.

### Fix Implemented

Added server-side authorization checks before allowing resource updates. The system now verifies the ownership of the requested resource using the logged-in user's session data and database records.

* Employees can only update tasks assigned to them.
* Managers and Admins can update any task based on their role.
* Unauthorized requests are blocked with a `403 Forbidden` response.

This prevents IDOR attacks by ensuring users can only perform actions on resources they are authorized to access.
