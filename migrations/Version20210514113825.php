<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210514113825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, comment_text VARCHAR(255) NOT NULL, post_id_FK INT NOT NULL, user_id_FK INT NOT NULL, INDEX IDX_9474526C5192178C (post_id_FK), INDEX IDX_9474526C244B603B (user_id_FK), UNIQUE INDEX constraint_unique_1 (post_id_FK, user_id_FK), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (post_id INT AUTO_INCREMENT NOT NULL, post_text VARCHAR(255) NOT NULL, created_on DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, user_id_FK INT NOT NULL, INDEX IDX_5A8A6C8D244B603B (user_id_FK), PRIMARY KEY(post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (user_id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, user_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mobile VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64924A232CF (user_name), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5192178C FOREIGN KEY (post_id_FK) REFERENCES post (post_id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C244B603B FOREIGN KEY (user_id_FK) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D244B603B FOREIGN KEY (user_id_FK) REFERENCES user (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5192178C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C244B603B');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D244B603B');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE user');
    }
}
