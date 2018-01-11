<?php

/*
 * This file is part of the DreamCommerce Shop AppStore package.
 *
 * (c) DreamCommerce
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Api\Resource;

use DreamCommerce\Component\ShopAppstore\Api\Resource;

final class Metafield extends Resource implements IdentifierAwareInterface
{
    /**
     * type of integer
     */
    const TYPE_INT = 1;

    /**
     * type of float
     */
    const TYPE_FLOAT = 2;

    /**
     * type of string
     */

    const TYPE_STRING = 3;

    /**
     * type of binary data
     */
    const TYPE_BLOB = 4;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'metafields';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierName(): string
    {
        return 'metafield_id';
    }

    // TODO
//    /**
//     * Read Resource
//     * @param mixed $args,... params
//     * @return \ArrayObject
//     * @throws ResourceException
//     */
//    public function get()
//    {
//        $query = $this->getCriteria();
//
//        $args = func_get_args();
//        if(empty($args)){
//            $args = array("system");
//        }
//
//        $isCollection = !$this->isSingleOnly && count($args)==1;
//
//        try {
//            $response = $this->client->request($this, 'get', $args, array(), $query);
//        } catch(ClientException $ex) {
//            throw new Resource\Exception\CommunicationException($ex->getMessage(), $ex);
//        }
//
//        return $this->transformResponse($response, $isCollection);
//    }
}