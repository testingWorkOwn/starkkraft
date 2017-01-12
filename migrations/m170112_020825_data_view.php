<?php

use yii\db\Migration;

class m170112_020825_data_view extends Migration
{


    public function safeUp()
    {
        $view = <<<VIEW
        CREATE VIEW data_view AS
        SELECT 
	        d1.id as id, 
	        d1.card_number as card_number, 
	        d1.date as date, 
	        (d1.volume * 100 + FLOOR(d2.volume * 100))/100 AS volume  ,
	        d1.service as service, 
	        d1.address_id as address_id
        FROM `data` as d1 
	        LEFT JOIN `data` as d2 ON d1.card_number = d2.card_number 
        WHERE (d1.address_id = d2.address_id 
            AND d1.id <> d2.id 
            AND d1.volume < 0 
            AND d2.volume > 0
            AND TIMEDIFF(d2.date, d1.date) < MAKETIME(0, 30,0) 
            AND TIMEDIFF(d2.date, d1.date) > MAKETIME(0, 0,0))
        UNION
        SELECT *
        FROM `data` AS d3
        WHERE NOT EXISTS (
	        SELECT * FROM `data` AS d4
	        WHERE 
            (
                d3.address_id = d4.address_id 
                AND d3.id <> d4.id
                AND 
                (
                    (
                        d3.volume < 0 
                        AND d4.volume > 0
                        AND TIMEDIFF(d4.date, d3.date) < MAKETIME(0, 30,0) 
                        AND TIMEDIFF(d4.date, d3.date) > MAKETIME(0, 0,0)
                    )
                    OR 
                    (
                        d3.volume > 0 
                        AND d4.volume < 0
                        AND TIMEDIFF(d3.date, d4.date) < MAKETIME(0, 30,0) 
                        AND TIMEDIFF(d3.date, d4.date) > MAKETIME(0, 0,0)
                    )
                )
            )
        )
VIEW;

        $this->execute(
            $view
        );
    }

    public function safeDown()
    {
    }
}
