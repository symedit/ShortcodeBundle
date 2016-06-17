<?php

/*
 * This file is part of the SymEdit package.
 *
 * (c) Craig Blanchette <craig.blanchette@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymEdit\Bundle\ShortcodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ShortcodeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $shortCodes = [];
        foreach ($container->findTaggedServiceIds('symedit_shortcode.shortcode') as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new \Exception(sprintf('No alias for shortcode "%s"', $id));
            }

            $shortCodeDefinition = $container->getDefinition($id);
            $shortCodeDefinition->addMethodCall('setSettings', [new Reference('symedit_shortcode.settings')]);

            $shortCodes[$attributes[0]['alias']] = new Reference($id);
        }

        $rendererDefinition = $container->getDefinition('symedit_shortcode.renderer');
        $rendererDefinition->replaceArgument(0, $shortCodes);
    }
}
