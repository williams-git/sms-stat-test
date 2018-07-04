<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=(!empty($this->title) ? validHTML($this->title, true):'')?></title>
	<meta name="description" content="<?=(!empty($this->description) ? validHTML($this->description, true):'')?>">
	<meta name="robots" content="<?if (!empty($this->noindex)): ?>noindex,nofollow<? else: ?>index,follow<? endif; ?>">
	<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css?v=1.1.1">
	<link rel="stylesheet" type="text/css" href="/css/style.css?v=1.1.2">
</head>
<body>

<div class="jumbotron text-center"><h1>SMS LOG STATISTICS</h1></div>

<div class="container-fluid"><?=$this->content?></div>

<div class="container"><div class="page-header"></div></div>

</body>
</html>
