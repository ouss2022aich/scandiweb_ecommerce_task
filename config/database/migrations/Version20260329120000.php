<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates orders and order_items tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE orders (
                id INT AUTO_INCREMENT NOT NULL,
                createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB',
        );

        $this->addSql(
            'CREATE TABLE order_items (
                id INT AUTO_INCREMENT NOT NULL,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                selectedAttributeItemIds JSON NOT NULL,
                INDEX IDX_BA14EE87C12469D5 (order_id),
                INDEX IDX_BA14EE874584665A (product_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB',
        );

        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_BA14EE87C12469D5 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_BA14EE874584665A FOREIGN KEY (product_id) REFERENCES products (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_BA14EE87C12469D5');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_BA14EE874584665A');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
    }
}
