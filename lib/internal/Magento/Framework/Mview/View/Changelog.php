<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Mview\View;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Phrase;

/**
 * Class \Magento\Framework\Mview\View\Changelog
 *
 * @since 2.0.0
 */
class Changelog implements ChangelogInterface
{
    /**
     * Suffix for changelog table
     */
    const NAME_SUFFIX = 'cl';

    /**
     * Column name of changelog entity
     */
    const COLUMN_NAME = 'entity_id';

    /**
     * Database connection
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     * @since 2.0.0
     */
    protected $connection;

    /**
     * View Id identifier
     *
     * @var string
     * @since 2.0.0
     */
    protected $viewId;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     * @since 2.0.0
     */
    protected $resource;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @since 2.0.0
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->checkConnection();
    }

    /**
     * Check DB connection
     *
     * @return void
     * @throws \Exception
     * @since 2.0.0
     */
    protected function checkConnection()
    {
        if (!$this->connection) {
            throw new \Exception('Write DB connection is not available');
        }
    }

    /**
     * Create changelog table
     *
     * @return void
     * @throws \Exception
     * @since 2.0.0
     */
    public function create()
    {
        $changelogTableName = $this->resource->getTableName($this->getName());
        if (!$this->connection->isTableExists($changelogTableName)) {
            $table = $this->connection->newTable(
                $changelogTableName
            )->addColumn(
                'version_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Version ID'
            )->addColumn(
                $this->getColumnName(),
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            );
            $this->connection->createTable($table);
        }
    }

    /**
     * Drop changelog table
     *
     * @return void
     * @throws ChangelogTableNotExistsException
     * @since 2.0.0
     */
    public function drop()
    {
        $changelogTableName = $this->resource->getTableName($this->getName());
        if (!$this->connection->isTableExists($changelogTableName)) {
            throw new ChangelogTableNotExistsException(new Phrase("Table %1 does not exist", [$changelogTableName]));
        }

        $this->connection->dropTable($changelogTableName);
    }

    /**
     * Clear changelog table by version_id
     *
     * @param int $versionId
     * @return boolean
     * @throws ChangelogTableNotExistsException
     * @since 2.0.0
     */
    public function clear($versionId)
    {
        $changelogTableName = $this->resource->getTableName($this->getName());
        if (!$this->connection->isTableExists($changelogTableName)) {
            throw new ChangelogTableNotExistsException(new Phrase("Table %1 does not exist", [$changelogTableName]));
        }

        $this->connection->delete($changelogTableName, ['version_id <= ?' => (int)$versionId]);

        return true;
    }

    /**
     * Retrieve entity ids by range [$fromVersionId..$toVersionId]
     *
     * @param int $fromVersionId
     * @param int $toVersionId
     * @return int[]
     * @throws ChangelogTableNotExistsException
     * @since 2.0.0
     */
    public function getList($fromVersionId, $toVersionId)
    {
        $changelogTableName = $this->resource->getTableName($this->getName());
        if (!$this->connection->isTableExists($changelogTableName)) {
            throw new ChangelogTableNotExistsException(new Phrase("Table %1 does not exist", [$changelogTableName]));
        }

        $select = $this->connection->select()->distinct(
            true
        )->from(
            $changelogTableName,
            [$this->getColumnName()]
        )->where(
            'version_id > ?',
            (int)$fromVersionId
        )->where(
            'version_id <= ?',
            (int)$toVersionId
        );

        return $this->connection->fetchCol($select);
    }

    /**
     * Get maximum version_id from changelog
     * @return int
     * @throws ChangelogTableNotExistsException
     * @throws \Exception
     * @since 2.0.0
     */
    public function getVersion()
    {
        $changelogTableName = $this->resource->getTableName($this->getName());
        if (!$this->connection->isTableExists($changelogTableName)) {
            throw new ChangelogTableNotExistsException(new Phrase("Table %1 does not exist", [$changelogTableName]));
        }
        $row = $this->connection->fetchRow('SHOW TABLE STATUS LIKE ?', [$changelogTableName]);
        if (isset($row['Auto_increment'])) {
            return (int)$row['Auto_increment'] - 1;
        } else {
            throw new \Exception("Table status for `{$changelogTableName}` is incorrect. Can`t fetch version id.");
        }
    }

    /**
     * Get changlog name
     *
     * Build a changelog name by concatenating view identifier and changelog name suffix.
     *
     * @throws \Exception
     * @return string
     * @since 2.0.0
     */
    public function getName()
    {
        if (strlen($this->viewId) == 0) {
            throw new \Exception("View's identifier is not set");
        }
        return $this->viewId . '_' . self::NAME_SUFFIX;
    }

    /**
     * Get changlog entity column name
     *
     * @return string
     * @since 2.0.0
     */
    public function getColumnName()
    {
        return self::COLUMN_NAME;
    }

    /**
     * Set view's identifier
     *
     * @param string $viewId
     * @return Changelog
     * @since 2.0.0
     */
    public function setViewId($viewId)
    {
        $this->viewId = $viewId;
        return $this;
    }

    /**
     * @return string
     * @since 2.0.0
     */
    public function getViewId()
    {
        return $this->viewId;
    }
}
