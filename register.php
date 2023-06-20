<?php
include('data/auth.php');

function validate($post, &$data, &$errors) {
    if(!isset($post['username']) || $post['username'] == "") $errors['username'] = "Adj meg egy felhasználónevet!";
    
    if(!isset($post['email']) || $post['email'] == "") $errors['email'] = "E-mail cím megadása kötelező!";
    elseif(filter_var($post['email'], FILTER_VALIDATE_EMAIL) == false) $errors['email'] = "Érvénytelen e-mail cím!";

    if(!isset($post['password']) || $post['password'] == "") $errors['password'] = "Jelszó megadása kötelező!";
    elseif(strcmp($post['password'], $post['password2']) != 0) $errors['password'] = "A két jelszó nem egyezik!";
    
    $data = $post;
    
    return count($errors) === 0;
}

$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$errors = [];
$data = [];
if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
    if ($auth->user_exists($data['username'])) {
        $errors['username'] = "A felhasználónév foglalt!";
    } else {
        $auth->register($data);
        header("Location: login.php");
        die();
    } 
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Regisztráció</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Főoldal</a>
                <ul class="navbar-nav ms-auto">
                    <li>
                        <a href="login.php" class="btn btn-light">Bejelentkezés</a>
                    </li>
                </ul>
        </div>
    </nav>
    <h1 class="display-1 text-center">Polly</h1>
    <div class="container">
        <div class="row text-center">
            <p class="lead">Szavazz te is a kedvenc egyetemedet érintő (fontos) kérdésekben!</p>
        </div>
    </div>

    <div class="container login">
        <div class="row keretes">
            <h2 class="text-center mb-4">Regisztráció</h2>
            <form action="register.php" method="post" class="needs-validation" novalidate>
                <div class="form-floating mb-4">
                    <input type="text" id="username" name="username" class="form-control<?= isset($errors['username']) ? " is-invalid" : ""?>" placeholder="Felhasználónév" value="<?= isset($data['username']) ? $data['username'] : ""?>">
                    <label for="username">Felhasználónév</label>
                    <div class="invalid-feedback">
                        <?= (isset($errors['username']) ? $errors['username'] : "")?>
                    </div>
                </div>
                <div class="form-floating mb-4">
                    <input type="email" id="email" name="email" class="form-control<?= isset($errors['email']) ? " is-invalid" : ""?>" placeholder="E-mail cím" value="<?= isset($data['email']) ? $data['email'] : ""?>">
                    <label for="email">E-mail cím</label>
                    <div class="invalid-feedback">
                        <?= (isset($errors['email']) ? $errors['email'] : "")?>
                    </div>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" id="password" name="password" class="form-control<?= isset($errors['password']) ? " is-invalid" : ""?>" placeholder="Jelszó">
                    <label for="password">Jelszó</label>
                    <div class="invalid-feedback">
                        <?= (isset($errors['password']) ? $errors['password'] : "")?>
                    </div>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" id="password2" name="password2" class="form-control" placeholder="Jelszó ismét">
                    <label for="password2">Jelszó ismét</label>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary mb-4">Regisztráció</button>
                </div>

                <div class="text-center">
                    <p>Már regisztráltál? <a href="login.php">Bejelentkezés</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>