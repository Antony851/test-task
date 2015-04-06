<?php

$schema = new \Doctrine\DBAL\Schema\Schema();

$post = $schema->createTable('peoples');
$post->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$post->addColumn('name', 'string', array('length' => 255));
$post->addColumn('lastname', 'string', array('length' => 255));
$post->addColumn('job_title', 'string', array('length' => 255));
$post->setPrimaryKey(array('id'));

$post = $schema->createTable('books');
$post->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$post->addColumn('name', 'string', array('length' => 255));
$post->addColumn('author', 'string', array('length' => 255));
$post->addColumn('page_count', 'integer', array('length' => 10));
$post->addColumn('human_id', 'integer', array('length' => 10));
$post->setPrimaryKey(array('id'));

return $schema;
