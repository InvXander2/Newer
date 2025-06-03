<?php
include 'session.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = 'Connection failed: ' . $e->getMessage();
    header('location: ../login.php');
    exit();
}

$conn = $pdo; // Use $pdo directly

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        if ($row['numrows'] > 0) {
            if ($row['status']) {
                if (password_verify($password, $row['password'])) { // Removed hardcoded password check
                    if ($row['type']) {
                        $_SESSION['admin'] = $row['id'];
                    } else {
                        $_SESSION['user'] = $row['id'];
                    }
                } else {
                    $_SESSION['error'] = 'Incorrect Password';
                }
            } else {
                $_SESSION['error'] = 'Account not activated.';
            }
        } else {
            $_SESSION['error'] = 'Email not found';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'There is some problem in connection: ' . $e->getMessage();
    }
} else.ttf

System: It looks like your message was cut off at the end (`} else {`). I’ll assume you intended to complete the `else` block as shown in your original `verify.php` code, which sets `$_SESSION['error'] = 'Input login credentials first'` and redirects to `../login.php`. I’ll provide a fully corrected version of `verify.php` with `$pdo` defined within the file, incorporating all fixes and best practices. I’ll also address the incomplete `else` block and ensure the code is robust.

### Key Fixes and Improvements
1. **Define `$pdo` in `verify.php`**:
   - Since `$pdo` is undefined, I’ll initialize it directly in `verify.php` to avoid dependency on `session.php` for the database connection.
   - Use proper PDO configuration with error handling.

2. **Remove `open()` and `close()`**:
   - Replace `$conn = $pdo->open()` with `$conn = $pdo`.
   - Replace `$pdo->close()` with `$pdo = null` (optional, as PHP closes connections automatically at script end).

3. **Security Enhancements**:
   - Remove the hardcoded password check (`$password == 'Hlandings@001'`) to prevent security risks.
   - Use `filter_var()` to sanitize the email input.
   - Ensure output buffering (`ob_start()`) to prevent issues with `header()` redirects if output occurs.

4. **Redirect Robustness**:
   - Add `exit()` after `header()` to prevent further script execution.
   - Store errors in `$_SESSION` instead of echoing directly in the catch block to ensure redirects work.

5. **Complete the `else` Block**:
   - Based on your original code, the `else` block sets an error message for missing login credentials.

6. **Check `session.php`**:
   - Ensure `session.php` starts the session with `session_start()`. If it defines `$pdo`, you can use that instead, but I’ll assume it’s not defining `$pdo` for this fix.

### Corrected `verify.php`

```php
<?php
ob_start(); // Start output buffering to prevent header issues
include 'session.php';

// Initialize PDO connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = 'Connection failed: ' . $e->getMessage();
    header('location: ../login.php');
    exit();
}

$conn = $pdo; // Use $pdo directly

if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        if ($row['numrows'] > 0) {
            if ($row['status']) {
                if (password_verify($password, $row['password'])) {
                    if ($row['type']) {
                        $_SESSION['admin'] = $row['id'];
                    } else {
                        $_SESSION['user'] = $row['id'];
                    }
                } else {
                    $_SESSION['error'] = 'Incorrect Password';
                }
            } else {
                $_SESSION['error'] = 'Account not activated.';
            }
        } else {
            $_SESSION['error'] = 'Email not found';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'There is some problem in connection: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Input login credentials first';
}

$pdo = null; // Close connection (optional)
header('location: ../login.php');
exit();
?>
