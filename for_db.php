<?

require_once 'config/connect.php';


/** Получаем наш ID статьи из запроса */
$name = trim($_POST['name']);
$surname = trim($_POST['surname']);
$age = intval($_POST['age']);

/** Если нам передали ID то обновляем */
if($name && $surname && $age){
	//вставляем запись в БД
	$query = $pdo->query("INSERT INTO `userss` VALUES(NULL, '$name', '$surname', '$age')");
	
	
	
	//извлекаем все записи из таблицы
	$query2 = $pdo->query("SELECT * FROM `userss` ORDER BY `id` DESC");

	while($row = $query2->fetch(PDO::FETCH_ASSOC)){
		$userss['id'][] = $row['id'];
		$userss['name'][] = $row['name'];
		$userss['surname'][] = $row['surname'];
		$userss['age'][] = $row['age'];
	}
	$message = 'Все хорошо';
}else{
	$message = 'Не удалось записать и извлечь данные';
}


/** Возвращаем ответ скрипту */

// Формируем масив данных для отправки
$out = array(
	'message' => $message,
	'users' => $userss
);

// Устанавливаем заголовот ответа в формате json
header('Content-Type: text/json; charset=utf-8');

// Кодируем данные в формат json и отправляем
echo json_encode($out);

