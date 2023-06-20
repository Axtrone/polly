<?php
include('data/auth.php');
include('data/pollrepo.php');

session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);

$loggedIn = $auth->is_authenticated();
$user = ($loggedIn) ? $auth->authenticated_user() : NULL;
$isAdmin = $auth->authorize(["admin"]);

$users = new UserStorage();
$polls = new PollStorage();

if(isset($_GET['logout'])){
    $auth->logout();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Főoldal</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Főoldal</span>
        <ul class="navbar-nav ms-auto">
            <?php if(!$loggedIn): ?>
                <li>
                    <a href="login.php" class="btn btn-light">Bejelentkezés</a>
                </li>
            <?php else: ?>
                <li class="nav-item dropdown">
                    <button class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        👤 <?=$user['username']?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" style="right: 0; left: auto;">
                        <?= ($isAdmin) ? '<li><a class="dropdown-item" href="makeVote.php">Új szavazás</a></li>' : "" ?>
                        <li><a class="dropdown-item" href="?logout">Kijelentkezés</a></li>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    </nav>
    <h1 class="display-1 text-center">Polly</h1>
    <div class="container">
        <div class="row text-center">
            <p class="lead">Szavazz te is a kedvenc egyetemedet érintő (fontos) kérdésekben!</p>
        </div>
    </div>
    <div id="ballots" class="container">
        <div id="current" class="row">
            <h2>Aktuális szavazások:</h2>
            <?php foreach ($polls->currentPolls() as $poll):?>
            <div class="col-md-6 col-xl-4 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-tite"><?=$poll['question']?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Azonosító: <?=$poll['id']?></h6>
                        <p class="card-text mb-1"><strong>Létrehozva: </strong><?=$poll['createdAt']?></p>
                        <p class="card-text"><strong>Határidő: </strong><?=$poll['deadline']?></p>
                        <?php 
                            if($loggedIn && $polls->voted($poll, $user['username'])) echo '<button type="button" class="btn btn-secondary" disabled>Már szavaztál</button>';
                            elseif($loggedIn) echo '<a href="vote.php?poll='.$poll['id'].'" class="btn btn-primary">Szavazás</a>';
                            else echo '<a href="login.php" class="btn btn-primary">Szavazás</a>';
                        ?>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
        <div id="expired" class="row my-4">
            <h2 id="expText">Lejárt szavazások:</h2>
            <?php foreach ($polls->expiredPolls() as $poll):?>
            <div class="col-md-6 col-xl-4 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-tite"><?=$poll['question']?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Azonosító: <?=$poll['id']?></h6>
                        <p class="card-text"><strong>Levárva: </strong><?=$poll['deadline']?></p>
                        <h6 class="card-subtitle mb-1"><strong>Eredmények:</strong></h6>
                        <table class="ms-2">
                            <?php foreach ($poll['options'] as $option):?>
                            <tr>
                                <td class="pe-2 display-8 text-decoration-underline"><?=$option?></td>
                                <td><?=(isset($poll['answers'][$option])? $poll['answers'][$option] : "0")?> fő</td>
                            </tr>
                            <?php endforeach;?>
                        </table>
                        <?= ($isAdmin) ? '<a href="vote.php?poll='.$poll['id'].'&delete" class="btn btn-secondary mt-1 float-end">🗑️</a>' : "" ?>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>