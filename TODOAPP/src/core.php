<?php
define('DATABASE_PATH', getcwd() . '/todo.json');
define('PREFIX', "TODO-");
const status = ['new', 'in-progress', 'done'];
function main(string $command, array $arguments)
{
    match ($command) {
        "list" => list_items(),
        "add" => add_item($arguments),
        "delete" => delete_item($arguments),
        "find" => find_item($arguments),
        "set" => set_status($arguments),
        "edit" => edit_item($arguments),
    };
}

function set_status(array $arguments)
{
    if (count($arguments) < 2) {
        print"You didn`t  provide item ID to change" . PHP_EOL . PHP_EOL;
        return;
    }

    $idElement = array_shift($arguments);
    $statusElement = array_shift($arguments);

    if (!array_search($statusElement, status)) {
        print "Status invalid" . PHP_EOL;
        return;
    }

    $items = get_items();

    foreach ($items as $key => $value) {
        if ($value['id'] === $idElement) {
            $items[$key]['status'] = $statusElement;
        }
    }
    save_to_file($items);
}

function list_items()
{
    print "## Todo items ##" . PHP_EOL;
    $items = get_items();
    if (empty($items)) {
        print "Nothing here yet..." . PHP_EOL . PHP_EOL;
        return;
    }
    foreach ($items as $item) {
        $state = $item['status'] === 'done' ? 'X' : ' '; # ctr + w
        print " - [$state] $item[id] from $item[created_at]" . PHP_EOL;
        print "   Content    : $item[content]" . PHP_EOL;
        print "   Status     : $item[status]" . PHP_EOL ;
        if (!empty($item['due_date'])){
            print "   Due-date   : $item[due_date]" . PHP_EOL . PHP_EOL;
        } else {
            print "\n";
        }
    }
}

function edit_item(array $arguments)
{
    if (count($arguments) < 2) {
        print"You didn`t  provide item ID to change" . PHP_EOL . PHP_EOL;
        return;
    }

    $idElement = array_shift($arguments);
    $contentElement= array_shift($arguments);

    $items = get_items();

    foreach ($items as $key => $value) {
        if ($value['id'] === $idElement) {
            $items[$key]['content'] =$contentElement;
        }
    }
    save_to_file($items);
}

function find_item(array $arguments)
{
    if (count($arguments) < 1) {
        print"You didn`t  provide item ID to delete" . PHP_EOL . PHP_EOL;
        return;
    }
    $items = get_items();
    $idToFind = array_shift($arguments);
    $itemFound = array_search($idToFind, array_column($items, 'id'));

    print_r($items[$itemFound]);
}

function delete_item(array $arguments)
{

    if (count($arguments) < 1) {
        print"You didn`t  provide item ID to delete" . PHP_EOL . PHP_EOL;
        return;
    }
    $idToDelete = array_shift($arguments);

    $items = get_items();
    $filtredItems = array_filter($items, fn($item) => $item['id'] !== $idToDelete);

    if (count($items) > count($filtredItems)) {
        save_to_file($filtredItems);
        print "Item $idToDelete was deleted" . PHP_EOL . PHP_EOL;
    } else {
        print "Nothing to delete " . PHP_EOL . PHP_EOL;
    }

}

function add_item(array $data)
{
    if (count($data) < 1) {
        print "You didn't provide item content." . PHP_EOL . PHP_EOL;
        return;
    }

    $lastId = 0;

    $items = get_items();

    if(count($items)!==0){
        $lastItems = $items[count($items) - 1];
        $lastId = (int)str_replace(PREFIX, "", $lastItems['id']);
    }
    $item = [
        'id' => PREFIX . ($lastId + 1),
        'created_at' => date('Y-m-d H:i'),
        'content' => array_shift($data),
        'status' => 'new',
    ];

    $dueDate = array_shift($data);
    if (!empty($dueDate)){
        $item['due_date']=date('Y-m-d H:i', strtotime($dueDate));
    }

    $items[] = $item;
    save_to_file($items);
    print "Item $item[id] was added." . PHP_EOL . PHP_EOL;
}

function get_items()
{
    if (!file_exists(DATABASE_PATH)) {
        save_to_file([]);
    }
    return json_decode(file_get_contents(DATABASE_PATH), true);
}

function save_to_file(array $items)
{
    file_put_contents(DATABASE_PATH, json_encode(array_values($items), JSON_PRETTY_PRINT));
}