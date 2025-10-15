<?php echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use Sylius\Component\Resource\Model\ResourceInterface;

class <?php echo $class_name; ?> extends AbstractResourcePlugin
{
    protected function addGeneralData(ResourceInterface $resource): void
    {
        if (!$resource instanceof \<?php echo $entity_class; ?>) {
            return;
        }

<?php echo $getter_calls; ?>

    }
}
