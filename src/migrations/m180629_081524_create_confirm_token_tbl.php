<?php

use yii\db\Migration;

/**
 * Class m180629_081524_create_confirm_token_tbl
 */
class m180629_081524_create_confirm_token_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%confirm_token}}', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->notNull(),
            'action' => $this->smallInteger()->notNull(),
            'token' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'attempt_no' => $this->integer()->unsigned()->defaultValue(1),
            'expires_at' => $this->timestamp(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180629_081524_create_confirm_token_tbl cannot be reverted.\n";
    
        $this->dropTable('{{%confirm_token}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180629_081524_create_confirm_token_tbl cannot be reverted.\n";

        return false;
    }
    */
}
