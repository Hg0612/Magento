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

namespace Lof\CouponCode\Model;

use Lof\CouponCode\Api\CouponRepositoryInterface;
use Lof\CouponCode\Api\Data\CouponSearchResultsInterfaceFactory;
use Lof\CouponCode\Api\Data\CouponInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lof\CouponCode\Model\ResourceModel\Coupon as ResourceCoupon;
use Lof\CouponCode\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CouponRepository implements couponRepositoryInterface
{

    protected $resource;

    protected $couponFactory;

    protected $couponCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataCouponFactory;

    private $storeManager;


    /**
     * @param ResourceCoupon $resource
     * @param CouponFactory $couponFactory
     * @param CouponInterfaceFactory $dataCouponFactory
     * @param CouponCollectionFactory $couponCollectionFactory
     * @param CouponSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCoupon $resource,
        CouponFactory $couponFactory,
        CouponInterfaceFactory $dataCouponFactory,
        CouponCollectionFactory $couponCollectionFactory,
        CouponSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->couponFactory = $couponFactory;
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCouponFactory = $dataCouponFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lof\CouponCode\Api\Data\CouponInterface $coupon
    ) {
        /* if (empty($coupon->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $coupon->setStoreId($storeId);
        } */
        try {
            $coupon->getResource()->save($coupon);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the coupon: %1',
                $exception->getMessage()
            ));
        }
        return $coupon;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($couponId)
    {
        $coupon = $this->couponFactory->create();
        $coupon->getResource()->load($coupon, $couponId);
        if (!$coupon->getId()) {
            throw new NoSuchEntityException(__('Coupon with id "%1" does not exist.', $couponId));
        }
        return $coupon;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->couponCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lof\CouponCode\Api\Data\CouponInterface $coupon
    ) {
        try {
            $coupon->getResource()->delete($coupon);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Coupon: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($couponId)
    {
        return $this->delete($this->getById($couponId));
    }
}
