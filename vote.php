<?php
include('data/auth.php');
include('data/pollrepo.php');

session_start();
$user_storage = new UserStorage();
$polls = new PollStorage();
$auth = new Auth($user_storage);

if (!$auth->is_authenticated() || !isset($_GET['poll']) || $polls->findById($_GET['poll']) == NULL) {
    header("Location: index.php");
    die();
}
$user = $auth->authenticated_user();
$poll = $polls->findById($_GET['poll']);

$type = ($poll['isMultiple']) ? "checkbox" : "radio";

if(isset($_GET['delete'])){
    if($auth->authorize(['admin'])) $polls->delete($_GET['poll']);
    header("Location: index.php");
    die();
}

$noChoice = false;
$success = false;
$options = [];
if ($_POST){
    if(!isset($_POST['choice'])){
        $noChoice = true;
    }
    else{
        $polls->vote($poll, $user['username'], $_POST['choice']);
        $success = true;
        header('Refresh: 5; URL=index.php');
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
    <title>Szavazás</title>
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
                        <?php if($auth->authorize(["admin"])){
                            echo '<li><a class="dropdown-item" href="?poll='.$_GET['poll'].'&delete">Törlés</a></li>';
                        }?>
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
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </symbol>
            <symbol id="check-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </symbol>
        </svg>
        <?php if($noChoice): ?>
            <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Warning:" width="24" height="24"><use xlink:href="#exclamation-triangle-fill"/></svg>
                <div>
                    <strong>Nem szavaztál</strong> semmire!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php elseif($success): ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" role="img"  width="24" height="24" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                <div>
                    <strong>Sikeres</strong> szavazás!
                </div>
            </div> 
        <?php endif; ?>
        <h1 class="display-6 mb-3"><?=$poll['question']?></h1>
        <p class="mb-1"><strong>Létrehozva: </strong><?=$poll['createdAt']?></p>
        <p><strong>Határidő: </strong><?=$poll['deadline']?></p>
        <form action="vote.php?poll=<?=$_GET['poll']?>" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Választási lehetőségek:</label>
                <?php foreach($poll['options'] as $opt): ?>
                <div class="form-check">
                    <input class="form-check-input" type="<?=$type?>" value="<?=$opt?>" id="<?=$opt?>" name="choice[]">
                    <label class="form-check-label" for="<?=$opt?>">
                        <?=$opt?>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="submitted">
            <button type="submit" class="btn btn-primary" <?=($success) ? "disabled" : "" ?>>Szavazás leadása</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>