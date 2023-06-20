<?php
include('data/auth.php');

function validate($post, &$data, &$errors) {
    if(!isset($post['username']) || $post['username'] == "") $errors['username'] = "Adj meg egy felhasználónevet!";
    if(!isset($post['password']) || $post['password'] == "") $errors['password'] = "Jelszó megadása kötelező!";
    $data = $post;

    return count($errors) === 0;
}

session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$data = [];
$errors = [];
if ($_POST) {
  if (validate($_POST, $data, $errors)) {
    $auth_user = $auth->authenticate($data['username'], $data['password']);
    if (!$auth_user) {
        $errors['global'] = "Hibás felhasználónév vagy jelszó!";
    } else {
        $auth->login($auth_user);
        header("Location: index.php");
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
    <title>Bejelentkezés</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Főoldal</a>
                <ul class="navbar-nav ms-auto">
                    <li>
                        <a href="register.php" class="btn btn-light">Regisztráció</a>
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
            <h2 class="text-center mb-4">Bejelentkezés</h2>
            <form action="login.php" method="post" class="needs-validation" novalidate>
                <?php if(isset($errors['global'])): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                        <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </symbol>
                    </svg>
                    <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                        <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Warning:" width="24" height="24"><use xlink:href="#exclamation-triangle-fill"/></svg>
                        <div>
                            <strong>Hibás</strong> felhasználónév vagy jelszó!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-floating mb-4">
                    <input type="text" id="username" name="username" class="form-control<?= isset($errors['username']) ? " is-invalid" : ""?>" placeholder="Felhasználónév" value="<?= isset($data['username']) ? $data['username'] : ""?>">
                    <label for="username">Felhasználónév</label>
                    <div class="invalid-feedback">
                        <?= (isset($errors['username']) ? $errors['username'] : "")?>
                    </div>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" id="password" name="password" class="form-control<?= isset($errors['password']) ? " is-invalid" : ""?>" placeholder="Jelszó">
                    <label for="password">Jelszó</label>
                    <div class="invalid-feedback">
                        <?= (isset($errors['password']) ? $errors['password'] : "")?>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary mb-4">Bejelentkezés</button>
                </div>

                <div class="text-center">
                    <p>Nincs felhasználód? <a href="register.php">Regisztráció</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>