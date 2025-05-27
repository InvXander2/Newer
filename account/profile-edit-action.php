<?php
include 'inc/session.php';
include '../admin/includes/slugify.php';
include '../inc/config.php';

$user_id = $_SESSION['user'];

if (isset($_POST['update'])) {
    $full_name = $_POST['full_name'];
    $uname = $_POST['uname'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $phone_no = $_POST['phone_no'];
    $nationality = isset($_POST['nationality']) && $_POST['nationality'] != '' ? $_POST['nationality'] : null;
    $photo = $_FILES['photo']['name'];

    $conn = $pdo->open();

    $filename = '';
    if (!empty($photo)) {
        move_uploaded_file($_FILES['photo']['tmp_name'], '../admin/images/' . $photo);
        $filename = $photo;
    } else {
        // Get current photo from DB
        $stmt = $conn->prepare("SELECT photo FROM users WHERE id=:id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch();
        $filename = $user['photo'];
    }

    try {
        $fields = [
            'full_name' => $full_name,
            'uname' => $uname,
            'gender' => $gender,
            'dob' => $dob,
            'phone_no' => $phone_no,
            'address' => $address,
            'photo' => $filename,
            'id' => $user_id
        ];

        $sql = "UPDATE users SET 
            full_name=:full_name,
            uname=:uname,
            gender=:gender,
            dob=:dob,
            phone_no=:phone_no,
            address=:address,
            photo=:photo";

        if ($nationality) {
            $sql .= ", nationality=:nationality";
            $fields['nationality'] = $nationality;
        }

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password=:password";
            $fields['password'] = $hashed_password;
        }

        $sql .= " WHERE id=:id";

        $stmt = $conn->prepare($sql);
        $stmt->execute($fields);

        $activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :start_date)");
        $activity->execute([
            'user_id' => $user_id,
            'message' => 'You updated your profile',
            'category' => 'Info',
            'start_date' => date('Y-m-d h:i A')
        ]);

        $_SESSION['success'] = 'Profile updated successfully.';
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Invalid request.';
}

header('location: profile.php');
exit;
?>
