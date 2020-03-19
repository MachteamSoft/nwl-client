<?php

namespace Mach\Bundle\NwlBundle\Client\Rest\Resource;

/**
 * @author Catalin Costache
 */
class Query extends AbstractResource
{

    /**
     * @return string
     */
    public function getUri()
    {
        return '/query';
    }

    /**
     * Get info about a query,
     * searching by query id OR by query name and site shortname
     *
     * @param integer (query ID) $query | array $query params
     * @return array
     */
    public function get($query)
    {
        if (!is_array($query)) {
            $query = array(
                'id' => $query
            );
        }
        if (!isset($query['id'])) {
            $query['id'] = 0;
        }

        return $this->performGet($query);
    }

    /**
     * Add a new query
     *
     * @param string $name
     * @param string $queryString
     * @param string $connection
     * @return array
     */
    public function post($name, $queryString, $connection = 'default')
    {
        return $this->performPost(array('name' => $name, 'queryString' => $queryString, 'connection' => $connection));
    }

    /**
     * Update query parameters
     *
     * @param int $queryId
     * @param array $params combination of queryString, sendgroup, name and connection
     * @return array
     */
    public function put($queryId, $params = array('queryString' => null, 'sendgroup' => null, 'name' => null, 'connection' => null))
    {
        return $this->performPut(array('id' => $queryId) + $params);
    }

    /**
     * Delete a query
     *
     * @param string $queryId
     * @return array
     */
    public function delete($queryId)
    {
        return $this->performDelete(array('id' => $queryId));
    }

}