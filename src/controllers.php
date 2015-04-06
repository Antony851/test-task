<?php
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views'
    )
);

//Страница людей
$app->get('/', function() use ($app) {
    
    $sql = "
    SELECT 
        p.id as id,
        p.name as name, 
        p.lastname as lastname, 
        p.job_title as job_title,
        b.name as bookname,
        b.author as bookauthor
    FROM peoples as p
    LEFT JOIN books as b ON (p.id = b.human_id) 
    ";
    $post = $app['db']->fetchall($sql);
    
    $templateVars = array(
        'include_temp' =>'peoples',
        'peoples' => $post
    );

    return $app['twig']->render('layout.twig', $templateVars);

});

//Страница книг
$app->get('/books/', function() use ($app) {
    
    $sql = "SELECT * FROM books";
    $post = $app['db']->fetchall($sql);
    
    $templateVars = array(
        'include_temp' =>'books',
        'books' => $post
    );

    return $app['twig']->render('layout.twig', $templateVars);

});

//Добавление человека
$app->post('/human_add/', function() use ($app) {
    
    $request = $app['request'];
    
    $name = $request->get('name');
    $lastname = $request->get('lastname');
    $job_title = $request->get('job_title');
    $book_id = $request->get('book');
    
    $result = array();
    
    $sql = "INSERT INTO peoples (
                id, name, lastname, job_title
            )
            VALUES (
                NULL , '$name', '$lastname', '$job_title'
            )";
    
    if($app['db']->query($sql)){
        $new_human_id = $app['db']->lastInsertId();
        $result['id'] = $new_human_id;
        $result['name'] = $name;
        $result['lastname'] = $lastname;
        $result['job_title'] = $job_title;
        
        $sql2 = "SELECT * FROM books WHERE id = $book_id";
        $post = $app['db']->fetchall($sql2);
        $result['book'] = $post[0]['name'] . ' - ' . $post[0]['author'];
        
        $sql3 = "UPDATE books SET human_id = $new_human_id WHERE id = $book_id";
        $app['db']->query($sql3);
        
        $result['success'] = 1;
    }
    
    
    
    $result = json_encode($result);
    return $result;
    
});

//Удаление человека
$app->post('/human_del/', function() use ($app) {
    
    $request = $app['request'];
    
    $id = $request->get('id');
    
    $result = array();
    
    $sql = "DELETE FROM peoples WHERE id = $id";
    if($app['db']->query($sql)){
        $result['id'] = $id;
        
        $sql2 = "UPDATE books SET human_id = 0 WHERE human_id = $id";
        $app['db']->query($sql2);
        
        $result['success'] = 1;
    }
    
    $result = json_encode($result);
    return $result;
    
});

//сохранение изменения человека
$app->post('/human_save/', function() use ($app) {
    
    $request = $app['request'];
    
    $id = $request->get('id');
    $name = $request->get('name');
    $lastname = $request->get('lastname');
    $job_title = $request->get('job_title');
    $book_id = $request->get('book_id');
    
    
    $result = array();
    $sql = "UPDATE peoples SET name = '$name', lastname = '$lastname', job_title = '$job_title' WHERE id = $id";
    
    if($app['db']->query($sql)){
        $result['id'] = $id;
        $result['name'] = $name;
        $result['lastname'] = $lastname;
        $result['job_title'] = $job_title;
        
        $sql4 = "SELECT * FROM books WHERE human_id = $id";
        $post2 = $app['db']->fetchall($sql4);
        
        $book_id2 = $post2[0]['id'];
        if($book_id2){
            $sql5 = "UPDATE books SET human_id = 0 WHERE id = $book_id2";
            $app['db']->query($sql5);
        }
        
        $sql3 = "UPDATE books SET human_id = $id WHERE id = $book_id";
        $app['db']->query($sql3);
        
        $sql2 = "SELECT * FROM books WHERE id = $book_id";
        $post = $app['db']->fetchall($sql2);
        $result['book'] = $post[0]['name'] . ' - ' . $post[0]['author'];
        
        $result['success'] = 1;
    }
    
    $result = json_encode($result);
    return $result;
    
});

//получение свободных книг
$app->get('/get_books/', function() use ($app) {
    
    $sql = "SELECT * FROM books WHERE human_id = 0";
    $books = $app['db']->fetchall($sql);
    $result = '';
    
    if($books){
        $result = "<select name='book'>";
        $result .= "<option></option>";
        foreach($books as $item){
            $id = $item['id'];
            $name = $item['name'] . '-' . $item['author'];
            $result .= "<option value='$id'>$name</option>";
        }
        $result .= "</select>";
    }
    
    return $result;
    
});

//Добавление книги
$app->post('/book_add/', function() use ($app) {
    
    $request = $app['request'];
    
    $name = $request->get('name');
    $author = $request->get('author');
    $page_count = $request->get('page_count');
    
    $result = array();
    
    $sql = "INSERT INTO books (
                id, name, author, page_count, human_id
            )
            VALUES (
                NULL , '$name', '$author', '$page_count', 0
            )";
    
    if($app['db']->query($sql)){
        $new_book_id = $app['db']->lastInsertId();
        $result['id'] = $new_book_id;
        $result['name'] = $name;
        $result['author'] = $author;
        $result['page_count'] = $page_count;
        
        $result['success'] = 1;
        
    }
    
    
    
    $result = json_encode($result);
    return $result;
    
});

//Удаление книги
$app->post('/book_del/', function() use ($app) {
    
    $request = $app['request'];
    
    $id = $request->get('id');
    
    $result = array();
    
    $sql = "DELETE FROM books WHERE id = $id";
    if($app['db']->query($sql)){
        $result['id'] = $id;
        $result['success'] = 1;
    }
    
    $result = json_encode($result);
    return $result;
    
});

//сохранение изменения книги
$app->post('/book_save/', function() use ($app) {
    
    $request = $app['request'];
    
    $id = $request->get('id');
    $name = $request->get('name');
    $author = $request->get('author');
    $page_count = $request->get('page_count');
    
    $result = array();
    $sql = "UPDATE books SET name = '$name', author = '$author', page_count = '$page_count' WHERE id = $id";
    
    if($app['db']->query($sql)){
        $result['id'] = $id;
        $result['name'] = $name;
        $result['author'] = $author;
        $result['page_count'] = $page_count;
        $result['success'] = 1;
    }
    
    $result = json_encode($result);
    return $result;
    
});
?>