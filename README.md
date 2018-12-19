# Wp-wizard
# Version 1.6
1. Задать пути в переменных к папке с WordPress установками в директории - `wp-wizard/install.php`
- Пример - ```$template_testing_folder``` = _'D:\OS\OpenServer\domains\localhost\2018'_;

2. Задать Site URL (локальный ip-адрес) при необходимости указать вложеные папки.
- Пример - ```$site_url``` = _"http://192.168.9.9/2018/"_;

3. Настройка подключения БД.
- Пример:
```$servername``` = _"localhost"_;
```$username``` = _"root"_;
```$password``` = "";

# Login Admin

- Login: _admin_
- Pass: _1_

# Video
https://youtu.be/6wCGbX67Otg

# CL Version 1.4 (12.12.2018)
- Реализация подгрузки ветки _master_ из репозитория в папку _wp-content/themes_. Checkbox _NEED GIT?_
Папка с темой именуется так, как записано в _style.css_ - после ```"Text Domain : _themeName_"```
За путь к репозиторию отвечает переменная ```$_link_to_git``` - вставка ссылки без PROD-ID

# CL Version 1.5 (18.12.2018)
- Поддерка WordPress 5.0
- Подгрузка ветки _package_ из репозитория в папку _wp-content/uploads_.

# CL Version 1.6 (19.12.2018)
- Разделение загрузки ветки _package_ и _master_ отдельными checkbox-ами.
- Выбор версии установки Селелектом при старте.