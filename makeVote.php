<?php
include('data/auth.php');
include('data/pollrepo.php');

$polls = new PollStorage();

function validate($post, &$options, &$errors) {
    if(!isset($post['title']) || $post['title'] == "") $errors['title'] = "Adj meg egy címet!";
    if(!isset($post['options'])) $errors['options'] = "Adj meg választási lehetőségeket!";
    else{
        $text = trim($post['options']);
        $options = explode("\n", str_replace("\r", "", $text));
        if(count($options) < 2) $errors['options'] = "Legalább két választási lehetőség szükséges!";
    }
    if($post['multiple'] == "default") $errors['multiple'] = "Válaszd ki a szavazás fajtáját!";
    if($post['deadline'] == "") $errors['deadline'] = "Határidő megadása kötelező!";
    elseif($post['deadline'] < date("Y-m-d")) $errors['deadline'] = "Határidő nem lehet a múltban!"; 

    return count($errors) === 0;
}


session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);

if (!$auth->authorize(["admin"])) {
    header("Location: index.php");
    die();
};
$user = $auth->authenticated_user();

$errors = [];
$options = [];
if (count($_POST) > 0 && validate($_POST, $options, $errors)) {
    $poll = [
        'id'         => uniqid(),
        'question'   => $_POST['title'],
        'options'    => $options,
        'isMultiple' => ($_POST['multiple'] == 'yes') ? true : false,
        'createdAt'  => date("Y-m-d"),
        'deadline'   => $_POST['deadline'],
        'answers'    => [],
        'voted'      => []
    ];
    $polls->add($poll);
    header("Location: index.php");
    die();
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
    <title>Szavazás létrehozás</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Főoldal</a>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                    <button class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        👤 <?=$user['username']?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" style="right: 0; left: auto;">
                        <li><a class="dropdown-item" href="index.php?logout">Kijelentkezés</a></li>
                    </ul>
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

    <div class="container poll">
        <div class="row keretes">
            <h1>Szavazás létrehozás</h1>
            <form action="makeVote.php" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="title" class="form-label">Cím:</label>
                    <input type="text" class="form-control<?= isset($errors['title']) ? " is-invalid" : ""?>" id="title" name="title" value="<?= isset($_POST['title']) ? $_POST['title'] : ""?>">
                    <div class="invalid-feedback">
                        <?= (isset($errors['title']) ? $errors['title'] : "")?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="options" class="form-label">Választási lehetőségek:</label>
                    <textarea name="options" id="options" class="form-control<?= isset($errors['options']) ? " is-invalid" : ""?>" cols="30" rows="4"><?= isset($_POST['options']) ? $_POST['options'] : ""?></textarea>
                    <div class="invalid-feedback">
                        <?= (isset($errors['options']) ? $errors['options'] : "")?>
                    </div>
                    <div class="form-text">Minden válaszlehetőséget új sorban kell kezdeni!</div>
                </div>
                <div class="mb-3">
                <select class="form-select<?= isset($errors['multiple']) ? " is-invalid" : ""?>" aria-label="" name="multiple">
                    <option value="default" <?=(count($_POST) < 1 || $_POST['multiple'] == "default") ? "selected" : "" ?>>Szavazás fajtája</option>
                    <option value="no" <?=(count($_POST) > 1 && $_POST['multiple'] == "no") ? "selected" : "" ?>>Egy választási lehetőség</option>
                    <option value="yes" <?=(count($_POST) > 1 && $_POST['multiple'] == "yes") ? "selected" : "" ?>>Több választási lehetőség</option>
                </select>
                <div class="invalid-feedback">
                    <?= (isset($errors['multiple']) ? $errors['multiple'] : "")?>
                </div>
                </div>
                <div class="input-group has-validation mb-3">
                    <span class="input-group-text" id="basic-addon1">Leadási határidő: </span>
                    <input type="date" name="deadline" id="deadline" class="form-control<?= isset($errors['deadline']) ? " is-invalid" : ""?>" value="<?= isset($_POST['deadline']) ? $_POST['deadline'] : ""?>">
                    <div class="invalid-feedback">
                        <?= (isset($errors['deadline']) ? $errors['deadline'] : "")?>
                    </div>
                </div>
                <fieldset class="input-group mb-3" disabled>
                    <span class="input-group-text" id="basic-addon1">Létrehozás ideje: </span>
                    <input type="date" name="createdAt" id="createdAt" class="form-control" value="<?=date("Y-m-d")?>">
                </fieldset>

                <button type="submit" class="btn btn-primary">Szavazás leadása</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>