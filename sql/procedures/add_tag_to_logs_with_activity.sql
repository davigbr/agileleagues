DELIMITER ;;

DROP PROCEDURE IF EXISTS add_tag_to_logs_with_activity;;

CREATE PROCEDURE add_tag_to_logs_with_activity (
	_activity_id INT,
	_tag_id INT
) MODIFIES SQL DATA

BEGIN 
		
	DECLARE _id INT UNSIGNED DEFAULT NULL;
	DECLARE _done INT DEFAULT FALSE;
	DECLARE _cursor CURSOR FOR 
		SELECT id FROM log WHERE log.activity_id = _activity_id;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = TRUE;

	OPEN _cursor;

	  	read_loop: LOOP
	    	FETCH _cursor INTO _id;
	    
	    IF _done THEN
	    	LEAVE read_loop;
	    END IF;
	   
	    INSERT INTO log_tag SET log_id = _id, tag_id = _tag_id;

		END LOOP;

	CLOSE _cursor;


END ;;

DELIMITER ;