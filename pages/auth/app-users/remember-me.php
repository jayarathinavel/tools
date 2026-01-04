<?php
    // Remember-me token helpers
    function createRememberToken($userId, $days = 30){
        $selector = bin2hex(random_bytes(12));
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + 60*60*24*$days);
        $conn = initDb();
        $sql = "INSERT INTO app_user_remember_tokens (user_id, selector, token_hash, expires_at) VALUES (?,?,?,?)";
        if($stmt = mysqli_prepare($conn,$sql)){
            mysqli_stmt_bind_param($stmt,"isss",$userId,$selector,$tokenHash,$expires);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        // Set secure cookie (HttpOnly)
        setcookie('remember_me', $selector.':'.$token, time()+60*60*24*$days, '/', '', isset($_SERVER['HTTPS']), true);
    }

    function restoreFromRemember(){
        // If already logged in, nothing to do
        if(isset($_SESSION['appUserLoggedIn']) && $_SESSION['appUserLoggedIn'] === true) return;
        if(empty($_COOKIE['remember_me'])) return;
        $parts = explode(':', $_COOKIE['remember_me']);
        if(count($parts) !== 2) return;
        list($selector,$token) = $parts;
        if(empty($selector) || empty($token)) return;
        $conn = initDb();
        $sql = "SELECT id, user_id, token_hash, expires_at FROM app_user_remember_tokens WHERE selector = ? LIMIT 1";
        if($stmt = mysqli_prepare($conn,$sql)){
            mysqli_stmt_bind_param($stmt,"s",$selector);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) !== 1){
                mysqli_stmt_close($stmt);
                return;
            }
            mysqli_stmt_bind_result($stmt,$id,$userId,$tokenHash,$expires);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            // Check expiry
            if(new DateTime($expires) < new DateTime()){
                // expired: delete and return
                $del = mysqli_prepare($conn, "DELETE FROM app_user_remember_tokens WHERE id = ?");
                if($del){ mysqli_stmt_bind_param($del, "i", $id); mysqli_stmt_execute($del); mysqli_stmt_close($del); }
                setcookie('remember_me', '', time()-3600, '/', '', isset($_SERVER['HTTPS']), true);
                return;
            }
            // Verify token
            if(hash_equals($tokenHash, hash('sha256', $token))){
                // Restore session
                $_SESSION['appUserLoggedIn'] = true;
                $_SESSION['appUserId'] = $userId;
                // Optionally rotate token: delete old and create a new one
                $del = mysqli_prepare($conn, "DELETE FROM app_user_remember_tokens WHERE id = ?");
                if($del){ mysqli_stmt_bind_param($del, "i", $id); mysqli_stmt_execute($del); mysqli_stmt_close($del); }
                createRememberToken($userId, 30);
            } else {
                // Possible theft: remove token and cookie
                $del = mysqli_prepare($conn, "DELETE FROM app_user_remember_tokens WHERE id = ?");
                if($del){ mysqli_stmt_bind_param($del, "i", $id); mysqli_stmt_execute($del); mysqli_stmt_close($del); }
                setcookie('remember_me', '', time()-3600, '/', '', isset($_SERVER['HTTPS']), true);
            }
        }
    }

    function deleteRememberTokensForUser($userId){
        $conn = initDb();
        $sql = "DELETE FROM app_user_remember_tokens WHERE user_id = ?";
        if($stmt = mysqli_prepare($conn,$sql)){
            mysqli_stmt_bind_param($stmt,"i",$userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        setcookie('remember_me', '', time()-3600, '/', '', isset($_SERVER['HTTPS']), true);
    }
