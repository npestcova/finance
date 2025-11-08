<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 1:24 PM
 */

namespace Application\Entity;

use Application\Dto\Transaction\ViewInfoDto;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="transaction")
 *
 * @ORM\Entity(repositoryClass="\Application\Repository\TransactionRepository")
 */
class Transaction extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", nullable=true)
     */
    protected $date;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    protected $account;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    protected $description;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=6, scale=2, nullable=false)
     */
    protected $amount = 0;

    /**
     * @return ViewInfoDto
     */
    public function getViewInfo()
    {
        $viewInfo = new ViewInfoDto();

        $viewInfo->id = $this->id;
        $viewInfo->description = $this->description;
        $viewInfo->dateDb = $this->date;
        $viewInfo->date = $this->date; //\Finance\Date::getViewDate($this->date);
        $viewInfo->amount = $this->amount;
        $viewInfo->accountId = $this->account
            ? $this->account->getId()
            : 0;
        $viewInfo->accountName = $this->account
            ? $this->account->getName()
            : Account::DEFAULT_NAME;
        $viewInfo->categoryId = $this->category
            ? $this->category->getId()
            : 0;
        $viewInfo->categoryName = $this->category
            ? $this->category->getFullName()
            : Category::DEFAULT_NAME;

        return $viewInfo;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}