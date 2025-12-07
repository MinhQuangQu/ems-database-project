DELIMITER $$

CREATE TRIGGER trg_before_salary_insert
BEFORE INSERT ON SALARY_PAYMENT
FOR EACH ROW
BEGIN
    IF NEW.payment_status IS NULL THEN
        -- 'Pending' means that payment calculated and approved.
        -- 'Unpaid' could mean a 0 or negative net pay (e.g., on leave).
        IF NEW.total_amount > 0 THEN
            SET NEW.payment_status = 'Pending';
        ELSE
            SET NEW.payment_status = 'Unpaid';
        END IF;
    END IF;
    -- If the payment_status is not NULL ( Paid or Pending )
END$$

DELIMITER ;