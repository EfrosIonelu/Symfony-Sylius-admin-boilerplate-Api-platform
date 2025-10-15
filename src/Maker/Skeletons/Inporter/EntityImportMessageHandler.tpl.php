<?php echo "<?php\n"; ?>

namespace <?php echo $namespace; ?>;

use App\Message\<?php echo $entityNamePasscalCase; ?>\<?php echo $messageClassName; ?>;
use App\MessageHandler\Main\AbstractImportMessageHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class <?php echo $className; ?> extends AbstractImportMessageHandler
{
    public function __invoke(<?php echo $messageClassName; ?> $importMessage): void
    {
        $data = $importMessage->getData();

        $this->import($data);
    }
}
