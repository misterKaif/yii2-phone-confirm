<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'bootstrap' => [
        \common\base\ContainerDefinitions::class,
        'queue',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'phoneConfirm' =>[
            'class' => \Teimur\YiiPhoneConfirm\Config::class,
            'user' => function (){
                return new \common\entities\user\User();
            },
            'find' => function(){
                return \common\entities\user\User::find();
            }
        ],
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db', // Компонент подключения к БД или его конфиг
            'tableName' => '{{%queue}}', // Имя таблицы
            'channel' => 'default', // Выбранный для очереди канал
            'mutex' => \yii\mutex\MysqlMutex::class, // Мьютекс для синхронизации запросов
        ],
        'monolog' => [
            'class' => \Mero\Monolog\MonologComponent::class,
            'channels' => [
                'main' => [
                    'handler' => [
                        [
                            'type' => 'stream',
                            'path' => '@app/runtime/logs/main_' . date('Y-m-d') . '.log',
                            'level' => 'debug'
                        ]
                    ],
                    'processor' => [],
                ],
            ],
        ],
    ],
];
