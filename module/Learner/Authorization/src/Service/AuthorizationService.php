<?php

namespace Authorization\Service;

use Authorization\Stdlib\Authorization;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;
use Doctrine\Common\Collections\ArrayCollection;

class AuthorizationService extends AbstractService
{

    /**
     * Current role
     *
     * @var Entity\AccessRoles
     */
    private $role;

    /**
     * Find ALL levels
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllLevels ()
    {
        $repository = $this->levelsRepository();

        // create query
        $qb = $repository->createQueryBuilder('level')
            ->select('level')
            ->add('orderBy', 'level.id ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ALL roles
     *
     * @return ArrayCollection
     */
    public function findAllRoles ()
    {
        $repository = $this->rolesRepository();

        // create query
        $dql = "SELECT role, rules, level
                FROM Savvecentral\Entity\AccessRoles role
                LEFT JOIN role.rules rules
                LEFT JOIN role.level level
                LEFT JOIN role.userRoles userRoles
                GROUP BY role.id
                ORDER BY role.level DESC, role.name ASC";
        $params = [];

        // execute query
        $results = $repository->fetchCollection($dql, $params);

        return $results;
    }

    /**
     * Find ALL roles by learner Id
     *
     * @param integer $learnerId
     * @return ArrayCollection
     */
    public function findAllRolesByLearnerId ($learnerId)
    {
        $repository = $this->rolesRepository();

        // create query
        $qb = $repository->createQueryBuilder('role')
            ->select('role')
            ->leftJoin('role.userRoles', 'userRoles')
            ->leftJoin('userRoles.learner', 'learner')
            ->where('learner.userId = :learnerId')
            ->setParameter('learnerId', $learnerId)
            ->add('orderBy', 'role.level DESC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ONE role by role ID
     *
     * @final Do not change this. This works great!
     * @param integer $id
     * @return Entity\AccessRoles
     */
    public function findOneRoleById ($id)
    {
        $repository = $this->rolesRepository();

        // create query
        $dql = "SELECT role, userRoles, learner, employment
                FROM Savvecentral\Entity\AccessRoles role
                LEFT JOIN role.userRoles userRoles
                LEFT JOIN userRoles.learner learner
                LEFT JOIN learner.employment employment
                WHERE role.id = :id
                ORDER BY learner.firstName ASC, learner.lastName ASC";
        $params = [
            'id' => $id
        ];

        // execute query
        $result = $repository->fetchOne($dql, $params);

        return $result;
    }

    /**
     * Find ONE role by name
     *
     * @param string $name
     * @param integer $siteId
     * @return Entity\AccessRoles
     */
    public function findOneRoleByName ($name, $siteId = null)
    {
        $repository = $this->rolesRepository();

        // create query
        $dql = [];
        $params = [];
        $dql[] = "SELECT role
                FROM Savvecentral\Entity\AccessRoles role
                LEFT JOIN role.userRoles userRoles
                LEFT JOIN userRoles.learner learner
                LEFT JOIN role.rules rules
                LEFT JOIN role.site site
                LEFT JOIN role.level level
                WHERE role.name = :name";
        $params['name'] = $name;

        if ($siteId) {
            $dql[] = "AND (site.siteId = :siteId OR site.siteId IS NULL)";
            $params['siteId'] = $siteId;
        }
        $dql[] = "GROUP BY learner.userId
                ORDER BY role.name, learner.firstName, learner.lastName";

        // execute query
        $dql = implode(' ', $dql);
        $results = $repository->fetchOne($dql, $params);

        return $results;
    }

    /**
     * Find ONE role by learner ID
     *
     * @param integer $learnerId
     * @return Entity\AccessRoles
     */
    public function findOneRoleByLearnerId ($learnerId)
    {
        $repository = $this->rolesRepository();

        // create query
        $qb = $repository->createQueryBuilder('role')
            ->select('role, userRoles, learner, rules, resource')
            ->leftJoin('role.userRoles', 'userRoles')
            ->leftJoin('userRoles.learner', 'learner')
            ->leftJoin('role.rules', 'rules')
            ->leftJoin('rules.resource', 'resource')
            ->where('learner.userId = :learnerId')
            ->setParameter('learnerId', $learnerId)
            ->add('orderBy', 'role.level DESC');

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Get the learner's roles
     *
     * @param integer $learnerId
     * @return array
     */
    public function learnerRoles ($learnerId)
    {
        $entityManager = $this->getEntityManager();
        $repository = $this->rolesRepository();

        // create query
        $params = [];
        $dql = "SELECT
                role.name AS name
                FROM Savvecentral\Entity\AccessRoles role
                LEFT JOIN role.userRoles userRoles
                LEFT JOIN userRoles.learner learner
                WHERE learner.userId = :learnerId
                ORDER BY role.level DESC";
        $params['learnerId'] = $learnerId;

        // execute query
        $results = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 24 * 7), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getArrayResult();

        // extract the role name
        $results = array_map(function  ($item)
        {
            return $item['name'];
        }, $results);

        return $results;
    }

    /**
     * Create ONE role
     *
     * @param array $data
     * @throws Exception\InvalidArgumentException
     * @return Entity\AccessRoles
     */
    public function createRole ($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);
            $entityManager = $this->getEntityManager();

            // get the level doctrine entity
            $levelId = isset($data['level_id']) ? $data['level_id'] : 1;
            $level = $entityManager->getReference('Savvecentral\Entity\AccessLevels', $levelId);

            // check if role is already in the system
            $role = $this->findOneRoleByName($data['name']);
            if (!$role) {
                $role = new Entity\AccessRoles();
            }
            $role = Stdlib\ObjectUtils::hydrate($data, $role);
            $role['status'] = 'active';
            $role['level'] = $level;

            // save into repository
            $entityManager->persist($role);
            $entityManager->flush($role);
            $entityManager->clear();

            return $role;
        }
        catch (\Exception $e) {
            throw new Exception\InvalidArgumentException(sprintf('Cannot create role'), null, $e);
        }
    }

    /**
     * Update ONE role
     *
     * @param array|\Traversable $data
     * @throws Exception\InvalidArgumentException
     * @return Entity\AccessRoles
     */
    public function updateRole ($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);
            $entityManager = $this->getEntityManager();

            // check if ID is given
            if (!(isset($data['id']) && $data['id'])) {
                throw new Exception\InvalidArgumentException(sprintf('Role ID is required for update'));
            }

            // get the level doctrine entity
            $levelId = isset($data['level_id']) ? $data['level_id'] : 1;
            $level = $entityManager->getReference('Savvecentral\Entity\AccessLevels', $levelId);

            // check if role is already in the system
            $role = $this->findOneRoleById($data['id']);
            $role = Stdlib\ObjectUtils::hydrate($data, $role);
            $role['level'] = $level;

            // save into repository
            $entityManager->persist($role);
            $entityManager->flush($role);
            $entityManager->clear();

            return $role;
        }
        catch (\Exception $e) {
            throw new Exception\InvalidArgumentException(sprintf('Cannot update role'), null, $e);
        }
    }

    /**
     * Delete ONE role
     *
     * @param string $name
     * @return Entity\AccessRoles
     */
    public function deleteRole ($name)
    {
        try {
            $entityManager = $this->getEntityManager();

            // find the role from repository
            $role = $this->findOneRoleByName($name);
            $role['status'] = 'deleted';

            // save into repository
            $entityManager->persist($role);
            $entityManager->flush($role);
            $entityManager->clear();

            return $role;
        }
        catch (\Exception $e) {
            throw new Exception\InvalidArgumentException(sprintf('Cannot delete role'), null, $e);
        }
    }

    /**
     * Activate ONE role
     *
     * @param integer $roleId
     * @return Entity\AccessRoles
     */
    public function activateRole ($roleId)
    {
        try {
            $entityManager = $this->getEntityManager();

            // find the role from repository
            $role = $entityManager->getReference('Savvecentral\Entity\AccessRoles', $roleId);
            $role['status'] = 'active';

            // save into repository
            $entityManager->persist($role);
            $entityManager->flush($role);
            $entityManager->clear();

            return $role;
        }
        catch (\Exception $e) {
            throw new Exception\InvalidArgumentException(sprintf('Cannot activate role'), null, $e);
        }
    }

    /**
     * Deactivate ONE role
     *
     * @param integer $roleId
     * @return Entity\AccessRoles
     */
    public function deactivateRole ($roleId)
    {
        try {
            $entityManager = $this->getEntityManager();

            // find the role from repository
            $role = $entityManager->getReference('Savvecentral\Entity\AccessRoles', $roleId);
            $role['status'] = 'inactive';

            // save into repository
            $entityManager->persist($role);
            $entityManager->flush($role);
            $entityManager->clear();

            return $role;
        }
        catch (\Exception $e) {
            throw new Exception\InvalidArgumentException(sprintf('Cannot deactivate role'), null, $e);
        }
    }

    /**
     * Find ALL resources
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllResources ()
    {
        $repository = $this->resourceRepository();

        // create query
        $qb = $repository->createQueryBuilder('resource')
            ->select('resource')
            ->add('orderBy', 'resource.title ASC, resource.type ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ONE resource by ID
     *
     * @param integer $id
     * @return Entity\AccessResources
     */
    public function findOneResourceById ($id)
    {
        $repository = $this->resourceRepository();
        $result = $repository->findOneById($id);
        return $result;
    }

    /**
     * Find ONE resource by resource name
     *
     * @param string $name
     * @return Entity\AccessResources
     */
    public function findOneResourceByName ($name)
    {
        $repository = $this->resourceRepository();
        $result = $repository->findOneByResource($name);
        return $result;
    }

    /**
     * Create ONE resource
     *
     * @param array|\Traversable $data
     * @throws Exception\IOException
     * @return Entity\AccessResources
     */
    public function createResource ($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);
            $entityManager = $this->getEntityManager();

            // find the level doctrine entity
            $levelId = isset($data['level_id']) ? $data['level_id'] : 1;
            $level = $entityManager->getReference('Savvecentral\Entity\AccessLevels', $levelId);

            // check if the resource already exists in the system
            $resource = $this->findOneResourceByName($data['resource']);

            // create new resource if not found in repository
            if (!$resource) {
                $resource = new Entity\AccessResources();
            }
            $resource = Stdlib\ObjectUtils::hydrate($data, $resource);
            $resource['level'] = $level;

            // persist and flush into repository
            $entityManager->persist($resource);
            $entityManager->flush($resource);
            $entityManager->clear();

            $resource_id = $resource['id'];

            // add matching rule if type is a 'route'
            if ($data['type'] == 'route') {

                $repository = $this->rolesRepository();
                $dql[] = "SELECT role.id
                    FROM Savvecentral\Entity\AccessRoles role
                    WHERE role.level = :level";

                $params['level'] = $levelId;
                // execute query
                $roleId = $repository->fetchOne($dql, $params);
                $role   = $entityManager->getReference('Savvecentral\Entity\AccessRoles', $roleId);
                $site   = null;
                $resource = $entityManager->getReference('Savvecentral\Entity\AccessResources', $resource_id);

                $rule = new Entity\AccessRules();
                $rule['role']         = $role;
                $rule['resource']     = $resource;
                $rule['site']         = $site;
                $rule['permission']   = 'allow';

                try {
                    // persist
                    $entityManager->persist($rule);
                    $entityManager->flush($rule);
        //            $entityManager->clear();

                } catch(\Exception $e) {
                    throw $e;
                }

            }
            return $resource;
        }
        catch (\Exception $e) {
            throw new Exception\IOException(sprintf('Cannot create resource'), null, $e);
        }
    }

    /**
     * Update ONE resource
     *
     * @param array|\Traversable $data
     * @throws Exception\InvalidArgumentException
     * @throws Exception\IOException
     * @return Entity\AccessResources
     */
    public function updateResource ($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);
            $entityManager = $this->getEntityManager();

            // check if ID is given
            if (!(isset($data['id']) && $data['id'])) {
                throw new Exception\InvalidArgumentException(sprintf('Resource ID is required for update'));
            }

            // find the level doctrine entity
            $levelId = isset($data['level_id']) ? $data['level_id'] : 1;
            $level = $entityManager->getReference('Savvecentral\Entity\AccessLevels', $levelId);

            // check if resource is in the repository
            $resource = $this->findOneResourceById($data['id']);
            $resource = Stdlib\ObjectUtils::hydrate($data, $resource);
            $resource['level'] = $level;

            // persist and flush into repository
            $entityManager->persist($resource);
            $entityManager->flush($resource);
            $entityManager->clear();

            // ensure that the Global Rule matching this resource has its level (role) value updated to match
            // -> but only if the type is 'route'
            if ($data['type'] == 'route') {

                // reference to the resource
                $resource_id = $data['id'];

                $repository = $this->ruleRepository();
                $dql[] = "SELECT rule
                    FROM Savvecentral\Entity\AccessRules rule
                    WHERE rule.resource = :id AND rule.site IS NULL";

                $params['id'] = $resource_id;
                // execute query
                $rule = $repository->fetchOne($dql, $params);

                // clear for new query
                $dql = $params = false;

                $repository = $this->rolesRepository();
                $dql[] = "SELECT role.id
                    FROM Savvecentral\Entity\AccessRoles role
                    WHERE role.level = :level";

                $params['level'] = $levelId;
                // execute query
                $roleId = $repository->fetchOne($dql, $params);
                $role = $roleId ? $entityManager->getReference('Savvecentral\Entity\AccessRoles', $roleId) : null;
                $rule['role'] = $role;

                // persist and flush into repository
                $entityManager->persist($rule);
                $entityManager->flush($rule);
                $entityManager->clear();
            }
            return $resource;
        }
        catch (\Exception $e) {
            throw new Exception\IOException(sprintf('Cannot update resource'), null, $e);
        }
    }

    /**
     * Delete ONE resource
     *
     * @param integer $id
     */
    public function deleteResource ($id)
    {
        try {
            $entityManager = $this->getEntityManager();

            // find the resource from repository
            $resource = $this->findOneResourceById($id);

            // save into repository
            $entityManager->remove($resource);
            $entityManager->flush($resource);
            $entityManager->clear();
        }
        catch (\Exception $e) {
            throw new Exception\IOException(sprintf('Cannot delete resource'), null, $e);
        }
    }

    /**
     * Find ALL rules assigned to the role
     *
     * @param integer $roleId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllRulesByRoleId ($roleId)
    {
        $repository = $this->ruleRepository();

        $dql = "SELECT rule, role, site, resource
                FROM Savvecentral\Entity\AccessRules rule
                LEFT JOIN rule.role role
                LEFT JOIN rule.resource resource
                LEFT JOIN rule.site site
                WHERE role.id <= :roleId
                ORDER BY rule.permission ASC, site.name ASC, resource.title ASC";
        $params['roleId'] = $roleId;

        // execute query
        $rules = $repository->fetchCollection($dql, $params);

        return $rules;
    }

    public function findOneRuleById ($id)
    {
        $entityManager = $this->getEntityManager();
        $repository = $this->ruleRepository();

        return $repository->findOneById($id);
    }

    /**
     * Fetch ALL permissions for one role ID only (used in the isAllowed View-Helper method)
     *
     * @param integer $roleId
     * @param string $type
     * @return array
     */
    public function fetchPermissionsByOneRoleId ($roleId, $type = null, $routeName = null)
    {
        $entityManager = $this->getEntityManager();

        // create query
        $dql = "SELECT
                rules.permission,
                resource.resource,
                resource.type,
                site.siteId,
                role.id as role_id
                FROM Savvecentral\Entity\AccessRules rules
                LEFT JOIN rules.role role
                LEFT JOIN rules.resource resource
                LEFT JOIN rules.site site
                WHERE role.id = :roleId
                ORDER BY resource.type, resource.resource";
        $params['roleId'] = $roleId;

        // execute query
        $repository = $this->ruleRepository();
        $results = $repository->fetchArray($dql, $params);

        $permissions = [];
        foreach ($results as $item) {
            if ($item['type'] == 'block') {
                // we need to pass the 'site' ID for the isAllowed method to interpret block resources
                $permissions[$item['type']][$item['siteId']][$item['resource']] = $item['permission'];

            } else {
                if ($item['role_id'] <= $roleId) {
                    $permissions[$item['type']][$item['resource']] = $item['permission'];
                }
            }
        }
        return isset($permissions[$type]) && $permissions[$type] ? $permissions[$type] : $permissions;
    }

    /**
     * Fetch ALL permissions for current role ID and also Less-Than role ID
     * This ensures that permissions for a 'lesser' role are also available for evaluation
     *
     * @param integer $roleId
     * @param string $type
     * @return array
     */
    public function fetchPermissionsByRoleId ($roleId, $type = null, $routeName = null)
    {
        $entityManager = $this->getEntityManager();

        // create query
        $dql = "SELECT
                rules.permission,
                resource.resource,
                resource.type,
                site.siteId,
                role.id as role_id
                FROM Savvecentral\Entity\AccessRules rules
                LEFT JOIN rules.role role
                LEFT JOIN rules.resource resource
                LEFT JOIN rules.site site
                WHERE role.id <= :roleId
                ORDER BY resource.type, resource.resource";
        $params['roleId'] = $roleId;

        // execute query
        $repository = $this->ruleRepository();
        $results = $repository->fetchArray($dql, $params);

        $permissions = [];
        foreach ($results as $item) {
            if ($item['type'] == 'block') {
                // we need to pass the 'site' ID for the isAllowed method to interpret block resources
                $permissions[$item['type']][$item['siteId']][$item['resource']] = $item['permission'];

            } else {
                if ($item['role_id'] <= $roleId) {
                    $resource = str_replace("*","", $item['resource']);
                    $permissions[$item['type']][$resource] = $item['permission'];
                }
            }
        }
        return isset($permissions[$type]) && $permissions[$type] ? $permissions[$type] : $permissions;
    }

    /**
     * Fetch ALL permissions by the current route
     *
     * @param integer $roleId
     * @param string $type
     * @return array
     */
    public function fetchPermissionsByRoute ($roleId, $type = null, $routeName = null)
    {
        $entityManager = $this->getEntityManager();

        // break the route name into segment to get the base - then also get rules for the base route
    //    $arr = explode("/", $routeName);
    //    $base = $arr[0];

        // create query
        $dql = [];
        $dql[] = "SELECT
            rules.permission,
            role.id as role_id,
            resource.resource,
            resource.type,
            site.siteId
            FROM Savvecentral\Entity\AccessRules rules
            LEFT JOIN rules.role role
            LEFT JOIN rules.resource resource
            LEFT JOIN rules.site site
            WHERE resource.resource = :route";
        $params['route'] = $routeName."*";

        /* !! this was a sound idea, but needs a lot more coding to work 'safely' !! */
    //    if ($base != $routeName) {
    //        $dql[] = "OR  resource.resource = :base";
    //        $params['base'] = $base."*";
    //    }

        $dql[] = "ORDER BY resource.type, resource.resource";
        $dql = implode(' ', $dql);

        // execute query
        $repository = $this->ruleRepository();
        $results = $repository->fetchArray($dql, $params);

        $permissions = [];
        foreach ($results as $item) {
            if ($item['type'] == 'block') {
                // we need to pass the 'site' ID for the isAllowed method to interpret block resources
                $permissions[$item['type']][$item['siteId']][$item['resource']] = $item['permission'];

            } else {
                if ($item['role_id'] <= $roleId) {
                    $permissions[$item['type']][$item['resource']] = $item['permission'];
                }
            }
        }
        return isset($permissions[$type]) && $permissions[$type] ? $permissions[$type] : $permissions;
    }

    /**
     * Create ONE rule
     *
     * @param integer $roleId
     * @param integer $resourceId
     * @param string $permission
     * @param integer $siteId
     * @throws Exception\IOException
     * @return Entity\AccessRules
     */
    public function createRule ($roleId, $resourceId, $permission, $siteId = null)
    {
        try {
            $entityManager = $this->getEntityManager();
            $site = $siteId ? $entityManager->getReference('Savvecentral\Entity\Site', $siteId) : null;
            $role = $entityManager->getReference('Savvecentral\Entity\AccessRoles', $roleId);
            $permission = (is_bool($permission) && $permission === false) ? 'deny' : ((null === $permission) || (is_bool($permission) && $permission === true) ? 'allow' : $permission);
            $resourceId = (null === $resourceId || empty($resourceId)) ? [] : (!is_array($resourceId) && is_numeric($resourceId) ? (array) $resourceId : $resourceId);
            sort($resourceId);

            // create rules for each resource selected
            foreach ($resourceId as $id) {
                // find if the current resource is already allocated to the current role in the current site
                $dql = [];
                $dql[] = "SELECT rules
                        FROM Savvecentral\Entity\AccessRules rules
                        LEFT JOIN rules.role role
                        LEFT JOIN rules.site site
                        LEFT JOIN rules.resource resource
                        WHERE role.id = :roleId
                        AND resource.id = :resourceId";
                $params['roleId'] = $roleId;
                $params['resourceId'] = $id;
                if ($siteId) {
                    $dql[] = "AND site.siteId = :siteId";
                    $params['siteId'] = $siteId;
                }
                else {
                    $dql[] = "AND site.siteId IS NULL";
                }
                // execute query
                $dql = implode(' ', $dql);
                $rule = $entityManager->createQuery($dql)
                    ->setParameters($params)
                    ->setMaxResults(1)
                    ->getOneOrNullResult();

                // if rule does not exists, create a new rule
                if (!$rule) {
                    $resource = $entityManager->getReference('Savvecentral\Entity\AccessResources', $id);
                    $rule = new Entity\AccessRules();
                    $rule['role'] = $role;
                    $rule['resource'] = $resource;
                    $rule['site'] = $site;
                }
                $rule['permission'] = $permission;

                // persist
                $entityManager->persist($rule);
                $entityManager->flush($rule);
            }
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Alias of self::createRule($roleId, $resourceId, $permission, $siteId)
     *
     * @todo Need to use this somewhere but currently I am not able to find where
     *
     * @param integer $roleId
     * @param integer $resourceId
     * @param integer $permission
     * @param integer $siteId
     * @return Entity\AccessRules
     */
    public function updateRule ($ruleId, $roleId, $resourceId, $permission, $siteId = null)
    {
        try {
            $entityManager = $this->getEntityManager();
            $repository = $this->ruleRepository();

            $rule = $repository->findOneById($ruleId);
            $site = $siteId ? $entityManager->getReference('Savvecentral\Entity\Site', $siteId) : null;
            $role = $roleId ? $entityManager->getReference('Savvecentral\Entity\AccessRoles', $roleId) : null;
            $resource = $resourceId ? $entityManager->getReference('Savvecentral\Entity\AccessResources', $resourceId) : null;

            // persist
        //    $entityManager->persist($rule);
        //    $entityManager->flush($rule);
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update ONE rule to have ALLOW permission
     *
     * @param integer $ruleId
     * @throws Exception
     * @return Entity\AccessRules
     */
    public function allowRule ($ruleId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $repository = $this->ruleRepository();
            $rule = $repository->findOneById($ruleId);
            $rule['permission'] = 'allow';

            // persist and flush into repository
            $entityManager->persist($rule);
            $entityManager->flush($rule);
            $entityManager->clear();

            return $rule;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update ONE rule to have DENY permission
     *
     * @param integer $ruleId
     * @throws Exception
     * @return Entity\AccessRules
     */
    public function denyRule ($ruleId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $repository = $this->ruleRepository();
            $rule = $repository->findOneById($ruleId);
            $rule['permission'] = 'deny';

            // persist and flush into repository
            $entityManager->persist($rule);
            $entityManager->flush($rule);
            $entityManager->clear();

            return $rule;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete ONE rule from repository
     *
     * @param integer $id
     * @throws Exception
     * @return boolean
     */
    public function deleteRule ($id)
    {
        try {
            $repository = $this->ruleRepository();
            $rule = $repository->findOneById($id);

            // delete
            $entityManager = $this->getEntityManager();
            $entityManager->remove($rule);
            $entityManager->flush($rule);
            $entityManager->clear();

            return true;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Find ALL learners in a role
     *
     * @param integer $roleId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllLearnersInRole ($roleId)
    {
        $repository = $this->learnerRepository();

        // create query
        $dql = [];
        $params = [];
        $dql[] = "SELECT learner, site, employment
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.site site
                LEFT JOIN site.platform platform
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.userRoles userRoles
                LEFT JOIN userRoles.role role
                WHERE role.id = :roleId
                ORDER BY learner.firstName ASC, learner.lastName ASC";
        $params['roleId'] = $roleId;

        // execute query
        $dql = implode(' ', $dql);
        $results = $repository->fetchCollection($dql, $params);

        return $results;
    }

    /**
     * Find ALL learners in a site
     *
     * @param integer $roleId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllLearnersInSite ($siteId)
    {
    	$repository = $this->learnerRepository();

    	// create query
    	$dql = [];
    	$params = [];
    	$dql[] = "SELECT learner
                FROM Savvecentral\Entity\Learner learner
                WHERE learner.site = :siteId AND learner.status IN (:status)
                ORDER BY learner.firstName ASC, learner.lastName ASC";
    	$params['siteId'] = $siteId;
    	$params['status'] = ['new','active'];

    	// execute query
    	$dql = implode(' ', $dql);
    	$results = $repository->fetchCollection($dql, $params);

    	return $results;
    }

    /**
     * Find ONE learner by learner ID
     *
     * @param integer $learnerId
     * @return Entity\Learner
     */
    public function findOneLearnerById ($learnerId)
    {
        $repository = $this->learnerRepository();

        // create query
        $qb = $repository->createQueryBuilder('learner')
            ->select('learner, userRoles, role')
            ->leftJoin('learner.userRoles', 'userRoles')
            ->leftJoin('userRoles.role', 'role')
            ->where('learner.userId = :learnerId')
            ->setParameter('learnerId', $learnerId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ALL roles allocated to the learner
     *
     * @param integer $learnerId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findLearnerRoles ($learnerId)
    {
        $repository = $this->rolesRepository();

        // create query
        $qb = $repository->createQueryBuilder('role')
            ->select('role')
            ->leftJoin('role.userRoles', 'userRoles')
            ->leftJoin('userRoles.learner', 'learner')
            ->where('learner.userId = :learnerId')
            ->setParameter('learnerId', $learnerId)
            ->add('orderBy', 'role.level ASC, role.title');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Add learners to the role
     *
     * @param array|integer|string $learnerId
     * @param integer $roleId
     * @throws Exception
     * @return boolean
     */
    public function addLearnerToRole ($learnerId, $roleId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $repository = $this->userRolesRepository();
            if (is_string($learnerId) || is_numeric($learnerId)) {
                $learnerId = (array) $learnerId;
            }

            $role = $entityManager->getReference('Savvecentral\Entity\AccessRoles', $roleId);
            foreach ($learnerId as $id) {
                $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $id);

                // check if learner is already assigned to the role
                $userRole = $repository->findOneBy([
                    'learner' => $learner,
                    'role' => $role
                ]);
                if ($userRole) {
                    continue;
                }

                // create a new user-role entity if not found
                $userRole = new Entity\AccessUserRoles();
                $userRole['learner'] = $learner;
                $userRole['role'] = $role;

                // save user-role in repository
                $entityManager->persist($userRole);
            }
            $entityManager->flush();
            $entityManager->clear();

            return true;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete learners from the role
     *
     * @param array|integer|string $learnerId
     * @param array|integer|string $roleId
     * @throws Exception
     * @return boolean
     */
    public function removeLearnerFromRole ($learnerId, $roleId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $repository = $this->userRolesRepository();
            if (is_string($learnerId) || is_numeric($learnerId)) {
                $learnerId = (array) $learnerId;
            }
            if (is_string($roleId) || is_numeric($roleId)) {
                $roleId = (array) $roleId;
            }

            if (!$learnerId) {
                return false;
            }

            if (!$roleId) {
                return false;
            }

            // create query
            $dql = "DELETE FROM Savvecentral\Entity\AccessUserRoles userRole
                    WHERE userRole.role IN (:roleIds)
                    AND userRole.learner IN (:learnerIds)";

            // execute query
            $results = $entityManager->createQuery($dql)
                ->setParameter('roleIds', $roleId)
                ->setParameter('learnerIds', $learnerId)
                ->execute();

            $entityManager->clear();
            return true;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if role has grant permission for the given resource
     *
     * @param string $role
     * @param string $resource
     * @param string $type
     * @return boolean
     */
    public function isGranted ($role, $resource, $type = 'route')
    {
        $role = $this->findOneRoleByName($role);
        $permissions = $role['permissions'];
        ksort($permissions);

        switch ($type) {
            default:
            case 'route':
                $granted = false;
                $routeName = $resource;
                $permissions = isset($permissions['route']) && is_array($permissions['route']) ? $permissions['route'] : [];
                foreach ($permissions as $route => $permission) {
                    if (fnmatch($route, $routeName, FNM_CASEFOLD)) {
                        $permission = $permissions[$route];

                        // is the current role granted access?
                        $granted = $permission === Authorization::ALLOW ? true : false;
                    }
                }
                break;

            case 'block':
                $granted = true;
                $templateName = $resource;
                $permissions = isset($permissions['block']) && is_array($permissions['block']) ? $permissions['block'] : [];
                foreach ($permissions as $template => $permission) {
                    if (fnmatch($template, $templateName, FNM_CASEFOLD)) {
                        $permission = $permissions[$template];

                        // is the current role granted access?
                        $granted = $permission === Authorization::ALLOW ? true : false;
                    }
                }
                break;
        }

        return $granted;
    }

    /**
     * Checks if the current user has a role
     *
     * @return boolean
     */
    public function hasRole ()
    {
        return $this->getRole() && $this->getRole() instanceof Entity\AccessRoles ? true : false;
    }

    /**
     * Get the role of the current user
     *
     * @return Entity\AccessRoles
     */
    public function getRole ()
    {
        if (is_string($this->role)) {
            $name = $this->role;
            $this->role = $this->findOneRoleByName($name);
        }

        return $this->role;
    }

    /**
     * Set the current role
     *
     * @param Entity\AccessRoles|string $role
     * @return \Authorization\Service\AuthorizationService
     */
    public function setRole ($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get the Access Control Level doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function levelsRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\AccessLevels');
        return $repository;
    }

    /**
     * Get the Access Control Roles doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function rolesRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\AccessRoles');
        return $repository;
    }

    /**
     * Get the Access Control Resources doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function resourceRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\AccessResources');
        return $repository;
    }

    /**
     * Get the Access Control Rules doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function ruleRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\AccessRules');
        return $repository;
    }

    /**
     * Get the Access Control User Roles doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function userRolesRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\AccessUserRoles');
        return $repository;
    }

    /**
     * Get the Access Control User Roles doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function learnerRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Learner');
        return $repository;
    }
}