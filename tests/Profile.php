<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace yiiunit\gii;

use yii\db\ActiveRecord;

class Profile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['description'], 'required'],
            [['description'], 'string', 'max' => 128]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
        ];
    }
}
