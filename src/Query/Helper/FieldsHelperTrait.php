<?php

/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\PFDB\Query\Helper;

trait FieldsHelperTrait
{
    private ?FieldsHelper $pfdbFieldsHelper = null;

    public function fields(): FieldsHelper
    {
        if (!$this->pfdbFieldsHelper) {
            $this->pfdbFieldsHelper = new FieldsHelper();
        }

        return $this->pfdbFieldsHelper;
    }
}
