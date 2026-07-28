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