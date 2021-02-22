<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Nuxeo OAuth2 Integration</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
</head>
<body style="padding-top: 50px">
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a href="#" class="navbar-brand">OAuth2 Login</a>
        </div>
        <?php if($authenticated): ?>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="#">Hi, <?php echo $username ?></a>
                </li>
            </ul>
        <?php else: ?>
            <div id="navbar" class="navbar-right navbar-form">
                    <a href="/login" class="btn btn-primary">Login with Nuxeo</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
    <div class="table-responsive">
        <?php if($authenticated): ?>
        <table class="table table-striped">
            <caption>Nuxeo Documents</caption>
            <thead>
                <tr>
                    <th scope="col">UID</th>
                    <th scope="col">Path</th>
                    <th scope="col">Type</th>
                    <th scope="col">State</th>
                    <th scope="col">Title</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($documents as $document): /** @var \Nuxeo\Client\Objects\Document */ ?>
                    <tr>
                        <td><?php echo $document->getUid() ?></td>
                        <td><?php echo $document->getPath() ?></td>
                        <td><?php echo $document->getType() ?></td>
                        <td><?php echo $document->getState() ?></td>
                        <td><?php echo $document->getTitle() ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>You need to authenticate to see documents.</p>
        <?php endif ?>
    </div>
</div>
</body>
</html>
