<?php
/**
 * Gatsby plugin for Craft CMS 3.x
 *
 * Plugin for enabling support for the Gatsby Craft CMS source plugin.
 *
 * @link      https://craftcms.com/
 * @copyright Copyright (c) 2020 Pixel & Tonic, Inc. <support@pixelandtonic.com>
 */

namespace craft\gatsbyhelper\services;

use craft\base\Component;
use craft\gatsbyhelper\events\RegisterSourceNodeTypesEvent;
use craft\gql\interfaces\elements\Asset as AssetInterface;
use craft\gql\interfaces\elements\Category as CategoryInterface;
use craft\gql\interfaces\elements\Entry as EntryInterface;
use craft\gql\interfaces\elements\GlobalSet as GlobalSetInterface;
use craft\gql\interfaces\elements\Tag as TagInterface;
use craft\gql\interfaces\elements\User as UserInterface;
use craft\helpers\Gql;

/**
 * SourceNodes Service
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 1.0.0
 *
 * @property-read array|mixed $sourceNodeTypes
 * @property-read array[]|mixed $sourcingData
 */
class SourceNodes extends Component
{
    /**
     * @event RegisterSourceNodesEvent The event that is triggered when registering source node types.
     *
     * Plugins get a chance to specify additional elements that should be Gatsby source nodes.
     *
     * ---
     * ```php
     * use craft\events\RegisterSourceNodeTypesEvent;
     * use craft\gatsbyhelper\services\SourceNodes;
     * use yii\base\Event;
     *
     * Event::on(SourceNodes::class, SourceNodes::EVENT_REGISTER_SOURCE_NODE_TYPES, function(RegisterSourceNodeTypesEvent $event) {
     *     $event->types[] = [
     *         'node' => 'book',
     *         'list' => 'books',
     *         'filterArgument' => 'type',
     *         'filterTypeExpression' => '(.+)_Book',
     *         'targetInterface' => BookInterface::getName(),
     *     ];
     * });
     * ```
     */
    const EVENT_REGISTER_SOURCE_NODE_TYPES = 'registerSourceNodeTypes';

    // Public Methods
    // =========================================================================

    /**
     * Return the query filters to use for querying source data with Gatsby
     *
     * @return mixed
     */
    public function getSourceNodeTypes(): array
    {
        $nodeTypes = [];

        if (Gql::canQueryEntries()) {
            $nodeTypes[] = [
                'node' => 'entry',
                'list' => 'entries',
                'filterArgument' => 'type',
                'filterTypeExpression' => '(?:.+)_(.+)_Entry+$',
                'targetInterface' => EntryInterface::getName(),
            ];
        }

        if (Gql::canQueryAssets()) {
            $nodeTypes[] = [
                'node' => 'asset',
                'list' => 'assets',
                'filterArgument' => 'volume',
                'filterTypeExpression' => '(.+)_Asset$',
                'targetInterface' => AssetInterface::getName(),
            ];
        }

        if (Gql::canQueryCategories()) {
            $nodeTypes[] = [
                'node' => 'category',
                'list' => 'categories',
                'filterArgument' => 'group',
                'filterTypeExpression' => '(.+)_Category$',
                'targetInterface' => CategoryInterface::getName(),
            ];
        }

        if (Gql::canQueryGlobalSets()) {
            $nodeTypes[] = [
                'node' => 'globalSet',
                'list' => 'globalSets',
                'filterArgument' => 'handle',
                'filterTypeExpression' => '(.+)_GlobalSet$',
                'targetInterface' => GlobalSetInterface::getName(),
            ];
        }

        if (Gql::canQueryTags()) {
            $nodeTypes[] = [
                'node' => 'tag',
                'list' => 'tags',
                'filterArgument' => 'group',
                'filterTypeExpression' => '(.+)_Tag$',
                'targetInterface' => TagInterface::getName(),
            ];
        }

        if (Gql::canQueryUsers()) {
            $nodeTypes[] = [
                'node' => 'user',
                'list' => 'users',
                'filterArgument' => '',
                'filterTypeExpression' => '',
                'targetInterface' => UserInterface::getName(),
            ];
        }

        $event = new RegisterSourceNodeTypesEvent([
            'types' => $nodeTypes
        ]);

        $this->trigger(self::EVENT_REGISTER_SOURCE_NODE_TYPES, $event);

        return $event->types;
    }
}
