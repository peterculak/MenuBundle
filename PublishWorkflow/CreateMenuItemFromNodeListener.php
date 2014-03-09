<?php

namespace Symfony\Cmf\Bundle\MenuBundle\PublishWorkflow;

use Symfony\Cmf\Bundle\MenuBundle\Event\CreateMenuItemFromNodeEvent;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\Menu;

/**
 * Listener for the CREATE_ITEM_FROM_NODE event that skips the node if it is
 * not published.
 *
 * @author Ben Glassman <bglassman@gmail.com>
 */
class CreateMenuItemFromNodeListener
{
    /**
     * @var SecurityContextInterface
     */
    private $publishWorkflowChecker;

    /**
     * The permission to check for when doing the publish workflow check.
     *
     * @var string
     */
    private $publishWorkflowPermission;

    /**
     * @param SecurityContextInterface $publishWorkflowChecker
     * @param string                   $attribute              the permission to check.
     */
    public function __construct(SecurityContextInterface $publishWorkflowChecker, $attribute = PublishWorkflowChecker::VIEW_ATTRIBUTE)
    {
        $this->publishWorkflowChecker = $publishWorkflowChecker;
        $this->publishWorkflowPermission = $attribute;
    }

    /**
     * Handle the event.
     *
     * @param CreateMenuItemFromNodeEvent $event
     */
    public function onCreateMenuItemFromNode(CreateMenuItemFromNodeEvent $event)
    {
        $node = $event->getNode();

        if (!$this->publishWorkflowChecker->isGranted($this->publishWorkflowPermission, $node)) {
            if ($node instanceof Menu) {
                $event->setItem($event->getFactory()->createItem($node->getName()));
            } else {
                $event->setSkipNode(true);
            }
        }
    }
}

