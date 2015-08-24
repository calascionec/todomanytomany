<?php


    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    $app = new Silex\Application();

    $app['debug'] = true;

    $server = 'mysql:host=localhost;dbname=to_do';
    $username = 'root';
    $password = '';
    $DB = new PDO($server, $username, $password);


    $app->register(new Silex\Provider\TwigServiceProvider(), 
        array('twig.path' => __DIR__.'/../views'
    ));

    // $app->get("/tasks", function() use ($app) {
    //     return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    // });

    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', 
            array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });

    $app->get("/tasks", function() use ($app) {
        return $app['twig']->render('tasks.html.twig', 
            array('tasks' => Task::getAll()));
    });

    $app->get("/categories", function() use ($app) {
        return $app['twig']->render('categories.html.twig', 
            array('categories' => Category::getAll()));
    });

    $app->get("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task.html.twig', 
            array('task' => $task, 'categories' => $task->getCategories(), 'all_categories' => Category::getAll()));
    });

    $app->get("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category.html.twig', 
            array('category' => $category, 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->get("/tasks/{id}/edit", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task.html.twig', 
            array( 'task' => $task, 
                   'categories' => $task->getCategories(), 
                   'all_categories' => Category::getAll() ) );
    });

    $app->get("/categories/{id}/edit", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category.html.twig', 
            array('category' => $category, 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });
    // $app->get("/categories", function() use ($app) {
    //     return $app['twig']->render('category.html.twig', array('categories' => Category::getAll()));
    // });

    // $app->get("/categories/{id}", function($id) use ($app) {
    //     $category = Category::find($id);
    //     return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
    // });

    //------------------------------------------------------

    $app->post("/tasks", function() use ($app) {
        // $description = $_POST['description'];
        // $due_date = $_POST['due_date'];
        $task = new Task(preg_quote($_POST['description'], "'"), "2015-10-10");
        $task->save();
        return $app['twig']->render('tasks.html.twig', 
            array('tasks' => Task::getAll()));
    });

    // $app->post("/tasks", function() use ($app) {
    //     $description = $_POST['description'];
    //     $due_date = $_POST['date'];
    //     $category_id = $_POST['category_id'];
    //     $task = new Task($description, $due_date, $id = null, $category_id);
    //     $task->save();
    //     $category = Category::find($category_id);
    //     return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
    // });

    $app->post("/categories", function() use ($app) {
        $category = new Category($_POST['name']);
        $category->save();
        return $app['twig']->render('categories.html.twig', 
            array('categories' => Category::getAll()));
    });

    // $app->post("/categories", function() use ($app) {
    //     $category = new Category($_POST['name']);
    //     $category->save();
    //     return $app['twig']->render('index.html.twig', 
    //          array('categories' => Category::getAll()));
    // });

    $app->post("/delete_categories", function() use ($app) {
        Category::deleteEverything();
        return $app['twig']->render('index.html.twig');
    });

    $app->post("/delete_tasks", function() use ($app) {
        $category_id = $_POST['category_id'];
        $category = Category::find($category_id);
        Task::deleteTasks($category_id);
        return $app['twig']->render('category.html.twig', 
            array('category' => $category));
    });

    $app->post("/results", function() use ($app) {
        // $category = new Category($_POST['find']);
        // $category->save();
        return $app['twig']->render('results.html.twig', 
            array('categories' => Category::getMatches($_POST['find'])));
    });


    $app->post("/add_tasks", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $category->addTask($task);
        return $app['twig']->render('category.html.twig', 
            array( 'category' => $category, 
                   'categories' => Category::getAll(), 
                   'tasks' => $category->getTasks(), 
                   'all_tasks' => Task::getAll() ) );
    });

    $app->post("/add_categories", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $task->addCategory($category);
        return $app['twig']->render('task.html.twig', 
            array( 'task' => $task, 'tasks' => Task::getAll(), 
                   'categories' => $task->getCategories(), 
                   'all_categories' => Category::getAll() ) );
    });

    return $app;



?>
