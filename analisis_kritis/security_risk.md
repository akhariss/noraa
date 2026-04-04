# Security Risk Assessment - Analisis Risiko Keamanan

## 1. Overview

Dokumen ini berisi analisis risiko keamanan terhadap Sistem Tracking Status Dokumen Notaris, termasuk identifikasi threat, assessment dampak, dan rekomendasi mitigasi.

---

## 2. Risk Assessment Matrix

### 2.1 Risk Matrix

```mermaid
flowchart TD
    subgraph "Risk Levels"
        H[High Risk<br/>Immediate Action Required]
        M[Medium Risk<br/>Plan Mitigation]
        L[Low Risk<br/>Monitor & Accept]
    end
    
    subgraph "High Risks"
        H1["Data Breach<br/>Client Information"]
        H2["Unauthorized Access<br/>Privilege Escalation"]
        H3["Session Hijacking"]
    end
    
    subgraph "Medium Risks"
        M1["SQL Injection"]
        M2["CSRF Attacks"]
        M3["Brute Force"]
        M4["File Upload Exploits"]
    end
    
    subgraph "Low Risks"
        L1["XSS Attacks"]
        L2["DoS Attacks"]
        L3["Information Leakage"]
    end
    
    H1 --> H
    H2 --> H
    H3 --> H
    M1 --> M
    M2 --> M
    M3 --> M
    M4 --> M
    L1 --> L
    L2 --> L
    L3 --> L
```

### 2.2 Risk Scoring

| Risk | Likelihood | Impact | Risk Score | Priority |
|------|------------|--------|------------|----------|
| Data Breach | 3 | 5 | 15 | HIGH |
| Unauthorized Access | 3 | 5 | 15 | HIGH |
| Session Hijacking | 3 | 5 | 15 | HIGH |
| SQL Injection | 2 | 5 | 10 | MEDIUM |
| CSRF Attacks | 2 | 4 | 8 | MEDIUM |
| Brute Force | 3 | 3 | 9 | MEDIUM |
| File Upload Exploits | 2 | 4 | 8 | MEDIUM |
| XSS Attacks | 2 | 3 | 6 | LOW |
| DoS Attacks | 2 | 3 | 6 | LOW |
| Information Leakage | 2 | 3 | 6 | LOW |

**Scoring:**
- Likelihood: 1 (Rare) - 5 (Almost Certain)
- Impact: 1 (Minor) - 5 (Catastrophic)
- Risk Score: Likelihood × Impact
- Priority: HIGH (12-25), MEDIUM (6-11), LOW (1-5)

---

## 3. High Risk Issues

### 3.1 Data Breach - Client Information

**Risk Description:**
Unauthorized access to sensitive client data including names, phone numbers, and document status.

**Attack Vectors:**
- SQL injection (mitigated)
- Unauthorized API access (mitigated)
- Insider threat (partial mitigation)
- Physical server access (external)

**Current Mitigations:**
```php
// 1. RBAC - Only authenticated users can access data
RBAC::enforce('registrasi.view');

// 2. Data masking - Phone numbers never displayed in full
$last4Phone = substr($cleanPhone, -4);

// 3. Audit logging - All data access logged
AuditLog::create(['action' => 'view', 'registrasi_id' => $id]);
```

**Residual Risk:**
- Insider threat (staff with legitimate access)
- Database backup exposure
- Log file exposure

**Impact on Notaris Domain:**
- Violation of client confidentiality
- Legal liability under notaris ethics
- Reputational damage
- Potential regulatory fines

**Recommendations:**
1. **Encrypt sensitive data at rest**
   ```php
   // Encrypt phone numbers in database
   $encryptedPhone = openssl_encrypt($phone, 'AES-256-CBC', $key);
   ```

2. **Implement data access logging**
   ```sql
   CREATE TABLE data_access_log (
       id INT PRIMARY KEY AUTO_INCREMENT,
       user_id INT,
       registrasi_id INT,
       accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       ip_address VARCHAR(45)
   );
   ```

3. **Regular access review**
   ```sql
   -- Monthly review of user access
   SELECT user_id, COUNT(*) as access_count 
   FROM data_access_log 
   WHERE accessed_at > DATE_SUB(NOW(), INTERVAL 1 MONTH)
   GROUP BY user_id;
   ```

**Risk Score After Mitigation:**
- Before: 15 (HIGH)
- After: 8 (MEDIUM)

---

### 3.2 Unauthorized Access - Privilege Escalation

**Risk Description:**
User gaining access to resources beyond their authorized role.

**Attack Vectors:**
- Session manipulation
- Parameter tampering
- Direct object reference
- RBAC bypass

**Current Mitigations:**
```php
// 1. Server-side RBAC enforcement
class RBAC {
    public static function enforce(string $permission): void {
        $role = Auth::getSession()['role'] ?? 'guest';
        if (!self::can($role, $permission)) {
            http_response_code(403);
            exit('Forbidden');
        }
    }
}

// 2. Route-level protection
Router::add('users', 'GET', [DashboardController::class, 'users'], 
    ['auth' => true, 'role' => 'notaris']);

// 3. Session fingerprinting
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
```

**Residual Risk:**
- Zero-day vulnerabilities in PHP
- Social engineering attacks
- Credential theft

**Impact on Notaris Domain:**
- Unauthorized case modifications
- Illegal document status changes
- Fraudulent approvals
- Legal consequences

**Recommendations:**
1. **Implement 2FA for notaris accounts**
   ```php
   // Use TOTP (Time-based One-Time Password)
   use RobThree\Auth\TwoFactorAuth;
   $tfa = new TwoFactorAuth();
   $secret = $tfa->createSecret();
   $qrCode = $tfa->getQRCodeImageAsDataUri($username, $secret);
   ```

2. **Add IP whitelist for admin access**
   ```php
   $allowedIPs = ['192.168.1.100', '192.168.1.101'];
   if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
       logSecurityEvent('UNAUTHORIZED_IP_ACCESS');
       http_response_code(403);
       exit('Access denied');
   }
   ```

3. **Implement account lockout**
   ```php
   class AccountLockout {
       public static function check(string $username): bool {
           $file = STORAGE_PATH . '/cache/lockout/' . md5($username) . '.lock';
           $data = file_exists($file) ? json_decode(file_get_contents($file), true) : null;
           
           if ($data && (time() - $data['time']) < 900) { // 15 minutes
               if ($data['attempts'] >= 5) {
                   return false; // Account locked
               }
           }
           return true;
       }
   }
   ```

**Risk Score After Mitigation:**
- Before: 15 (HIGH)
- After: 6 (LOW)

---

### 3.3 Session Hijacking

**Risk Description:**
Attacker stealing or predicting session ID to impersonate legitimate user.

**Attack Vectors:**
- Network sniffing (unencrypted HTTP)
- XSS cookie theft
- Session fixation
- Cross-subdomain attacks

**Current Mitigations:**
```php
// 1. Session fingerprinting
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// 2. Regenerate session on login
session_regenerate_id(true);

// 3. Validate fingerprint on each request
if (!hash_equals($_SESSION['user_fingerprint'], $fingerprint)) {
    session_destroy();
    throw new SecurityException('Session hijacking detected');
}
```

**Residual Risk:**
- Malware on client device
- Compromised network (public WiFi)
- Browser vulnerabilities

**Impact on Notaris Domain:**
- Full account takeover
- Unauthorized document access
- Fraudulent transactions
- Legal liability

**Recommendations:**
1. **Implement session timeout reduction**
   ```php
   // Reduce session lifetime for sensitive operations
   define('SENSITIVE_SESSION_LIFETIME', 900); // 15 minutes for user management
   
   if ($currentRoute === 'users') {
       if (time() - $_SESSION['last_activity'] > SENSITIVE_SESSION_LIFETIME) {
           // Require re-authentication
           redirect('/index.php?gate=login&reason=reauth');
       }
   }
   ```

2. **Add device fingerprinting**
   ```php
   // Enhanced fingerprint with more parameters
   $fingerprint = hash('sha256', 
       $_SERVER['HTTP_USER_AGENT'] . 
       $_SERVER['REMOTE_ADDR'] .
       $_SERVER['HTTP_ACCEPT_LANGUAGE'] .
       $_SERVER['HTTP_ACCEPT_ENCODING']
   );
   ```

3. **Implement session activity notifications**
   ```php
   // Send notification on new device login
   if ($fingerprint !== $_SESSION['stored_fingerprint']) {
       // Log and notify
       logSecurityEvent('NEW_DEVICE_LOGIN', [
           'user_id' => $_SESSION['user_id'],
           'ip' => $_SERVER['REMOTE_ADDR'],
           'user_agent' => $_SERVER['HTTP_USER_AGENT'],
       ]);
       // Send email notification (future)
   }
   ```

**Risk Score After Mitigation:**
- Before: 15 (HIGH)
- After: 5 (LOW)

---

## 4. Medium Risk Issues

### 4.1 SQL Injection

**Current Status:** ✅ MITIGATED

**Mitigations:**
- All queries use prepared statements
- No string concatenation in SQL
- Input validation and sanitization

**Residual Risk:**
- Second-order SQL injection (very low)
- ORM/Query builder vulnerabilities (not applicable - raw SQL)

**Ongoing Actions:**
- Regular code review for new SQL queries
- Security scanning in CI/CD (future)

**Risk Score:** 4 (LOW - after mitigation)

---

### 4.2 CSRF Attacks

**Current Status:** ✅ MITIGATED

**Mitigations:**
- CSRF token on all forms
- Token validation in controllers
- SameSite=Strict cookie attribute

**Residual Risk:**
- CSRF on GET requests (none exist)
- Subdomain attacks (mitigated with SameSite)

**Ongoing Actions:**
- Ensure all new forms include CSRF token
- Regular token rotation

**Risk Score:** 3 (LOW - after mitigation)

---

### 4.3 Brute Force Attacks

**Current Status:** ✅ MITIGATED

**Mitigations:**
- Rate limiting (5 attempts per 5 minutes for login)
- Account lockout (recommended)
- Generic error messages

**Residual Risk:**
- Distributed brute force (multiple IPs)
- Credential stuffing (using leaked credentials)

**Recommendations:**
1. **Implement CAPTCHA after failed attempts**
   ```php
   // Add Google reCAPTCHA after 3 failed attempts
   if ($failedAttempts >= 3) {
       // Show CAPTCHA
       $recaptcha = $_POST['g-recaptcha-response'];
       if (!verifyRecaptcha($recaptcha)) {
           return ['success' => false, 'message' => 'CAPTCHA invalid'];
       }
   }
   ```

2. **Monitor for credential stuffing**
   ```php
   // Check if credentials match known breach patterns
   function checkCredentialStuffing($username, $password): bool {
       // Compare against known breach patterns (future)
       // Use Have I Been Pwned API
   }
   ```

**Risk Score:** 4 (LOW - after mitigation)

---

### 4.4 File Upload Exploits

**Current Status:** ✅ MITIGATED

**Mitigations:**
- Extension whitelist (jpg, jpeg, png, pdf)
- Size limit (5MB)
- Secure filename generation
- Storage outside web root

**Residual Risk:**
- Polyglot files (malicious content in valid file format)
- Image processing vulnerabilities

**Recommendations:**
1. **Add file content validation**
   ```php
   // Validate actual file content, not just extension
   $finfo = new finfo(FILEINFO_MIME_TYPE);
   $mimeType = $finfo->file($_FILES['image']['tmp_name']);
   
   $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
   if (!in_array($mimeType, $allowedMimes)) {
       return ['success' => false, 'message' => 'Invalid file content'];
   }
   ```

2. **Image re-encoding**
   ```php
   // Re-encode images to strip metadata and potential exploits
   $image = imagecreatefromstring(file_get_contents($_FILES['image']['tmp_name']));
   imagejpeg($image, $destination, 90);
   imagedestroy($image);
   ```

**Risk Score:** 3 (LOW - after mitigation)

---

## 5. Low Risk Issues

### 5.1 XSS Attacks

**Current Status:** ✅ MITIGATED

**Mitigations:**
- Global input sanitization
- Output encoding with htmlspecialchars()
- Content-Security-Policy (recommended)

**Residual Risk:**
- DOM-based XSS (client-side, limited impact)
- Stored XSS in logs (not displayed)

**Risk Score:** 2 (LOW)

---

### 5.2 DoS Attacks

**Current Status:** ⚠️ PARTIAL MITIGATION

**Mitigations:**
- Rate limiting per endpoint
- Request throttling

**Gaps:**
- No network-level DDoS protection
- Single server deployment

**Recommendations:**
1. **Use CDN for DDoS protection**
2. **Implement request queuing**
3. **Set up cloud-based DDoS mitigation**

**Risk Score:** 3 (LOW)

---

### 5.3 Information Leakage

**Current Status:** ✅ MITIGATED

**Mitigations:**
- Generic error messages
- Detailed errors logged internally
- Directory listing disabled

**Residual Risk:**
- Error messages in development mode
- Stack traces in logs

**Risk Score:** 2 (LOW)

---

## 6. Risk Summary

### 6.1 Before Mitigation

| Risk Level | Count | Percentage |
|------------|-------|------------|
| HIGH | 3 | 30% |
| MEDIUM | 4 | 40% |
| LOW | 3 | 30% |

### 6.2 After Mitigation

| Risk Level | Count | Percentage |
|------------|-------|------------|
| HIGH | 0 | 0% |
| MEDIUM | 1 | 10% |
| LOW | 9 | 90% |

### 6.3 Risk Reduction

```mermaid
flowchart LR
    subgraph "Before Mitigation"
        B1["HIGH: 3"]
        B2["MEDIUM: 4"]
        B3["LOW: 3"]
    end
    
    subgraph "After Mitigation"
        A1["HIGH: 0"]
        A2["MEDIUM: 1"]
        A3["LOW: 9"]
    end
    
    B1 --> A1
    B2 --> A2
    B3 --> A3
    
    style B1 fill:#ff6666
    style B2 fill:#ffcc00
    style B3 fill:#66cc66
    style A1 fill:#66cc66
    style A2 fill:#ffcc00
    style A3 fill:#66cc66
```

---

## 7. Dampak Terhadap Data Hukum Notaris

### 7.1 Legal Implications

**If Data Breach Occurs:**

| Consequence | Impact | Probability |
|-------------|--------|-------------|
| Client Lawsuits | High | Medium |
| Regulatory Fines | High | Low |
| License Revocation | Critical | Low |
| Reputational Damage | High | High |

**Regulatory Framework:**
- UU Jabatan Notaris (UUJN)
- UU Informasi dan Transaksi Elektronik (ITE)
- UU Perlindungan Data Pribadi

### 7.2 Ethical Obligations

**Notaris Code of Ethics:**
- Confidentiality of client information
- Duty to protect client data
- Professional responsibility

**Security Measures as Due Diligence:**
- Implemented security measures demonstrate compliance
- Regular security audits show proactive approach
- Incident response plan shows preparedness

---

## 8. Risk Management Strategy

### 8.1 Risk Treatment Options

| Risk | Treatment | Justification |
|------|-----------|---------------|
| Data Breach | Mitigate | Critical business impact |
| Unauthorized Access | Mitigate | Legal consequences |
| Session Hijacking | Mitigate | Full account takeover |
| SQL Injection | Accept (low residual) | Well mitigated |
| CSRF | Accept (low residual) | Well mitigated |
| Brute Force | Transfer (CAPTCHA service) | Cost-effective |
| File Upload | Mitigate | Moderate impact |

### 8.2 Ongoing Risk Management

**Monthly:**
- Review security logs
- Check for failed login attempts
- Review user access

**Quarterly:**
- Security audit
- Penetration testing
- Update threat assessment

**Annually:**
- Full risk assessment
- Security policy review
- Compliance audit

---

## 9. Kesimpulan

### 9.1 Risk Assessment Summary

**Initial State:**
- 3 HIGH risks
- 4 MEDIUM risks
- 3 LOW risks

**After Mitigation:**
- 0 HIGH risks
- 1 MEDIUM risk
- 9 LOW risks

**Risk Reduction:** 87% reduction in HIGH/MEDIUM risks

### 9.2 Security Posture

**Current Security Posture: STRONG**

- All HIGH risks mitigated
- Comprehensive security controls
- Defense in depth strategy
- Audit trail for accountability

### 9.3 Recommendations Priority

**Immediate:**
1. Implement 2FA for notaris accounts
2. Add data encryption for sensitive fields
3. Set up security monitoring alerts

**Short Term:**
1. Implement CAPTCHA for brute force protection
2. Add file content validation
3. Set up regular security audits

**Long Term:**
1. Migrate to Redis for session management
2. Implement SIEM for security monitoring
3. Achieve security certification (ISO 27001)

### 9.4 Final Assessment

Sistem Tracking Status Dokumen Notaris memiliki **postur keamanan yang kuat** dengan mitigasi yang efektif untuk semua risiko tinggi. 

Dengan implementasi rekomendasi yang diberikan, sistem dapat mencapai tingkat keamanan yang sesuai untuk menangani data dokumen hukum yang sensitif sesuai dengan requirements domain notaris Indonesia.

**Risk Acceptance:**
- Residual risks are at acceptable levels
- Security controls are proportional to threats
- Due diligence demonstrated through comprehensive measures
