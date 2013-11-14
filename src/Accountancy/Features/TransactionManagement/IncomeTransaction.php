<?php
/**
 *
 */

namespace Accountancy\Features\TransactionManagement;

use Accountancy\Entity\Category;
use Accountancy\Entity\User;
use Accountancy\Features\FeatureException;

/**
 * Class IncomeTransaction
 *
 * @package Accountancy\Features\TransactionManagement
 */
class IncomeTransaction
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var integer
     */
    protected $accountId;

    /**
     * @var integer
     */
    protected $currencyId;

    /**
     * @var integer
     */
    protected $categoryId;

    /**
     * @var integer
     */
    protected $counterpartyId;

    /**
     * @var double
     */
    protected $amount = 0.0;

    /**
     * @param \Accountancy\Entity\User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param integer $accountId
     *
     * @return $this
     */
    public function setAccountId($accountId)
    {
        $this->accountId = (int) $accountId;

        return $this;
    }

    /**
     * @param double $amount
     */
    public function setAmount($amount)
    {
        $this->amount = (double) $amount;
    }

    /**
     * @param integer $currencyId
     *
     * @return $this
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = (int) $currencyId;

        return $this;
    }

    /**
     * @param integer $categoryId
     *
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = (int) $categoryId;

        return $this;
    }

    /**
     * @param integer $counterpartyId
     *
     * @return $this
     */
    public function setCounterpartyId($counterpartyId)
    {
        $this->counterpartyId = (int) $counterpartyId;

        return $this;
    }

    /**
     * @throws \Accountancy\Features\FeatureException
     */
    public function run()
    {
        $account = $this->user->findAccountById($this->accountId);

        if (is_null($account)) {
            throw new FeatureException("Account doesn't exist");
        }

        if ($account->getCurrencyId() != $this->currencyId) {
            throw new FeatureException("Currency is't supported by account");
        }

        $category = $this->user->findCategoryById($this->categoryId);

        if (is_null($category)) {
            throw new FeatureException("Category doesn’t exist");
        }

        $counterparty = $this->user->findCounterpartyById($this->counterpartyId);

        if (is_null($counterparty)) {
            throw new FeatureException("Counterparty doesn’t exist");
        }

        if ($this->amount <= 0.0) {
            throw new FeatureException("Amount of money should be greater than zero");
        }

        $accounts = $this->user->getAccounts();

        foreach ($accounts as $key => $value) {

            if ($value->getId() === $account->getId()) {
                $accounts[$key]->increaseBalance($this->amount);
            }
        }

        $this->user->setAccounts = $accounts;
    }
}
