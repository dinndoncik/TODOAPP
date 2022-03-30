<?php
//[X]list-show all todos
//[X]add <id>, description, due data- add a new todo item
//[X]delete id -delete a todo item

//HW
# [X] search <query> find todo items
# [X] edit <id> <content> update todo item
# [X] set-status <id> <status> (check if status is new, in-progrss, done or rejected)
# [X] *Task 3 - add due-date to todo item (if due-date is in past, then show status 'outdated'

require_once __DIR__ . "/src/core.php";

$script = array_shift($argv); // app.php
$command = array_shift($argv); // list

$args = $argv; // []

main($command,$args);







































