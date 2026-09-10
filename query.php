<?php
$trialBalance = "
    CREATE OR REPLACE VIEW view_trial_balance AS SELECT
    `account_transactions`.`project_id` as `project_id`,
    `account_transactions`.`voucher_type` as `voucher_type`,
    `account_transactions`.`voucher_no` as `voucher_no`,
    `account_transactions`.`voucher_date` as `voucher_date`,
    `account_transactions`.`coa_setup_id` as `coa_setup_id`,
    `account_transactions`.`coa_head_code` as `coa_head_code`,
    `account_transactions`.`debit_amount` as `debit_amount`,
    `account_transactions`.`credit_amount` as `credit_amount`,
    `coa_setups`.`transaction` as `transaction`,
    `coa_setups`.`general` as `general`,
    `coa_setups`.`parent_id` as `parent_id`,
    `coa_setups`.`head_type` as `head_type`,
    `coa_setups`.`head_name` as `head_name`
    FROM `account_transactions`
    LEFT JOIN `coa_setups` ON `coa_setups`.`id` = `account_transactions`.`coa_setup_id`
    ";
