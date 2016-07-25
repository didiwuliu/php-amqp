--TEST--
AMQPQueue::consume from nonexistent queue
--SKIPIF--
<?php if (!extension_loaded("amqp")) print "skip"; ?>
--FILE--
<?php
function noop () {return false;}

$cnn = new AMQPConnection();
$cnn->setReadTimeout(10); // both are empirical values that should be far enough to deal with busy RabbitMQ broker
$cnn->setWriteTimeout(10);
$cnn->connect();

$ch = new AMQPChannel($cnn);

// Declare a new exchange
$ex = new AMQPExchange($ch);
$ex->setName('exchange-' . microtime(true));
$ex->setType(AMQP_EX_TYPE_FANOUT);
$ex->declareExchange();

// Create a new queue
$q = new AMQPQueue($ch);
$q->setName('nonexistent-' . microtime(true));

try {
	$q->consume('noop');
} catch (Exception $e) {
	echo get_class($e), ': ', $e->getMessage();
}

?>
--EXPECTF--
AMQPQueueException: Server channel error: 404, message: NOT_FOUND - no queue 'nonexistent-%f' in vhost '/'
