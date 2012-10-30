<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121030112258 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
        
        $this->addSql("CREATE SEQUENCE fos_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE fos_group (id INT NOT NULL DEFAULT nextval('fos_group_id_seq'), name VARCHAR(255) NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_4B019DDB5E237E06 ON fos_group (name)");
        $this->addSql("COMMENT ON COLUMN fos_group.roles IS '(DC2Type:array)'");
        $this->addSql("CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY(user_id, group_id))");
        $this->addSql("CREATE INDEX IDX_B3C77447A76ED395 ON fos_user_user_group (user_id)");
        $this->addSql("CREATE INDEX IDX_B3C77447FE54D947 ON fos_user_user_group (group_id)");
        $this->addSql("ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        
        $groups = array (
            'Students'            => array(),
            'Moderators'          => array('ROLE_MODERATOR'),
            'Administrators'      => array('ROLE_ADMIN'),
            'Tutors'              => array(),
            'Professors'          => array(),
            'Staff'               => array(),
            'MemberOfStudente'    => array(), 
            'MemberOfDocente'     => array(), 
            'MemberOfPersonaleTA' => array(), 
            'MemberOfEmpty'       => array()
        );
        
        $sql = 'INSERT INTO fos_group (name, roles) VALUES (?, ?)';
        foreach($groups as $group => $roles) {
            $this->addSql($sql, array($group, serialize($roles)));
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
        
        $this->addSql("DROP TABLE fos_group CASCADE");
        $this->addSql("DROP TABLE fos_user_user_group CASCADE");
        $this->addSql("DROP SEQUENCE fos_group_id_seq");
    }
}
