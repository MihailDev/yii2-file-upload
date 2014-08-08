Yii 2 File Upload
===========================

## Установка

Удобнее всего установить это расширение через [composer](http://getcomposer.org/download/).

Либо запустить

```
php composer.phar require --prefer-dist mihaildev/yii2-file-upload "*"
```

или добавить

```json
"mihaildev/yii2-file-upload": "*"
```

в разделе `require` вашего composer.json файла.


## Настройка Behavior для модели

```php
	public function behaviors()
	{
		return [
			'file-upload' => [
				'class' => FileUploadBehavior::className(),
				/*
				'replacePairs' => [
					'<modelId>' => 'id',// данный набор установлен по умолчанию
				],
				*/
				'attributes' => [
					'file' =>[
						//'handler' => FileUploadBehavior::HANDLER_FILE, // установлен по умолчанию
						'path' => '@webroot/files/<modelId>/<fileName>.<fileExtension>',
						'url' => '@web/files/<modelId>/<fileName>.<fileExtension>',
					],
					'ava' => [
						'handler' => FileUploadBehavior::HANDLER_IMAGE,
						'path' => '@webroot/files/avatar/<modelId>/origin.<fileExtension>',
						'url' => '@web/files/avatar/<modelId>/origin.<fileExtension>',
						'imagine' => function($filename){
								return Image::aligning($filename, 800, 800);
							},
						'saveOptions'=> ['quality' => 90],
						'thumbs' => [
							'icon' => [
								'path' => '@webroot/files/avatar/<modelId>/icon.<fileExtension>',
								'url' => '@web/files/avatar/<modelId>/icon.<fileExtension>',
								'imagine' => function($filename){ return Image::thumbnail($filename, 50, 50);},
								'saveOptions'=> ['quality' => 70],
							],
							'preview' => [
								'path' => '@webroot/files/avatar/<modelId>/preview.<fileExtension>',
								'url' => '@web/files/avatar/<modelId>/preview.<fileExtension>',
								'imagine' => function($filename){ return Image::thumbnail($filename, 200, 200);},
								'saveOptions'=> ['quality' => 90],
							]
						]
					]
				]
			],
		];
	}
```

Замечание!!! аттрибуты должны храниться в базе!

Обработка изображений производится с помощью библиотеки https://github.com/yiisoft/yii2-imagine

Функци Image::aligning не входит в стандартную библиотеку тут я использую свою библиотеку https://github.com/MihailDev/yii2-imagine

для получения пути в модели воспользуйтесь функцией $this->getUploadedFilePath($attributeName);
для получения ссылки в модели воспользуйтесь функцией $this->getUploadedFileUrl($attributeName);

Для изображений
$this->getUploadedFilePath($attributeName); - получить путь на основное изображение
$this->getUploadedFilePath($attributeName, $thumbId); - получить путь на дополнительное изображение

$this->getUploadedFileUrl($attributeName); - получить ссылку на основное изображение
$this->getUploadedFileUrl($attributeName, $thumbId); - получить ссылку на дополнительное изображение

пример на основе показанных выше настроек

$this->getUploadedFilePath('file');
$this->getUploadedFileUrl('file');

$this->getUploadedFilePath('ava');
$this->getUploadedFileUrl('ava');

$this->getUploadedFilePath('ava', 'icon');
$this->getUploadedFileUrl('ava', 'icon');

$this->getUploadedFilePath('ava', 'preview');
$this->getUploadedFileUrl('ava', 'preview');


## Настройка и использование виджета
для файлов
```php
<?= $form->field($model, 'file')->widget(\mihaildev\fileupload\FileUploadWidget::className(),[
					'fileUrl' => $model->getUploadedFileUrl('file'),
					'fileName' => 'Скачать'
					]

				) ?>
```

для изображений
```php
<?= $form->field($model, 'ava')->widget(\mihaildev\fileupload\ImageUploadWidget::className(),[
					'imageOptions' => ['width' => '200'],
					'imageUrl' => $model->getUploadedFileUrl('ava', 'preview')
					]

				) ?>
```


