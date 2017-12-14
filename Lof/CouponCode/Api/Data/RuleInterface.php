<?php
/**
 * Lof CouponCode is a powerful tool for managing the processing return and exchange requests within your workflow. This, in turn, allows your customers to request and manage returns and exchanges directly from your webstore. The Extension compatible with magento 2.x
 * Copyright (C) 2017  Landofcoder.com
 * 
 * This file is part of Lof/CouponCode.
 * 
 * Lof/CouponCode is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Lof\CouponCode\Api\Data;

interface RuleInterface
{

    const RULE_ID = 'rule_id';
    const COUPON_RULE_ID = 'coupon_rule_id';


    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId();

    /**
     * Set rule_id
     * @param string $rule_id
     * @return \Lof\CouponCode\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId);

    /**
     * Get coupon_rule_id
     * @return string|null
     */
    public function getCouponRuleId();

    /**
     * Set coupon_rule_id
     * @param string $coupon_rule_id
     * @return \Lof\CouponCode\Api\Data\RuleInterface
     */
    public function setCouponRuleId($coupon_rule_id);
}
