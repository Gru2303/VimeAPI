# VimeAPI

## ***VimeAPI - Что это?***
**VimeAPI** - Это библиотека PHP для управления своим аккаунтом или бота.  С помощью данной библиотеки вы можете автоматизировать свой магазин вимеров.  Также вы можете изменять пароль, отключать или включать двухэтапную аутентификацию и т. д.  
В Общем все что можно сделать через личный кабинет VimeWorld

## ***VimeAPI - Документация***
1. Подключения библиотеки
	```php
	define('Grusha-VimeAPI', true);
	require('VimeAPI/VimePHP.php');
	```
1. Инициализация класса
	```php
	$VimeAPI = new VimeAPI('PHPSESSID', 'Dev Token');
	```

#### Возможности
1. Передать вимеры игроку
	```php
	$VimeAPI->giveVimers('Игрок', Количество вимеров);
	```
1. Изменить пароль
	```php
	$VimeAPI->changePassword('Старый пароль', 'Новый пароль');
	```
1. Отключить двухэтапную аутентификацию
	```php
	$VimeAPI->disableTwoFA('Код');
	```
1. Включить двухэтапную аутентификацию
	```php
	$VimeAPI->EnableTwoFA('Пароль');
	```
	После включения двухэтапной аутентификацию, все данные сохраняются в файле VimeSave/NickName.txt
	
	
1. Получить количество вимеров
	```php
	$VimeAPI->getVimers();
	```
1. Получить история операций
	```php
	$VimeAPI->getOperationsHistory();
	```
1. Получить всю информацию об аккаунте
	```php
	$VimeAPI->getInformations();
	```

## ***F.A.Q.***
1. ##### Где найти PHPSESSID?
	- Заходим на https://cp.vimeworld.ru/
	
		![](/img/one/1.png)
	- Авторизируемся на аккаунт бота
	
		![](/img/one/2.png)
	- Открываем консоль браузера(CTRL + Shift + I)
	
		![](/img/one/3.png)
	- Вписываем код
		```js
		console.log(`Ваш токен сессии в Личном кабинете: ${window.getCookie("PHPSESSID")}`);
		```
		
	- Получаем PHPSESSID
	
		![](https://i.imgur.com/MpuS4Pc.png)
1. ##### Где взять DEV Token VimeWorld?
	- Зайти в лаунчер VimeWorld
	
		![](/img/two/1.png)
	- Авторизоваться на аккаунт бота и нажать "ИГРАТЬ"
	
		![](/img/two/2.png)
	- Открыть чат(/)
	
		![](/img/two/3.png)
	- Вписать команду "/api dev"
	
		![](/img/two/4.png)
	- Получаем DEV Token
	
		![](/img/two/5.png)

## ***Контакты***
Telegram: [https://t.me/Gru2303](https://t.me/Gru2303)  
VK: [https://vk.com/id287088154](https://vk.com/id287088154)

## ***Автору на кофе***
##### QIWI: +380674260607

## ***Лицензия***
Этот проект лицензирован под лицензией Apache - для получения дополнительной информации [LICENSE](https://github.com/Gru2303/VimeAPI/blob/master/LICENSE).
