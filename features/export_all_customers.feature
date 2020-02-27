@export_customers
Feature: Export customers
    In order to manage customer relationship
    As a Store Owner
    I want to export customers to a csv file

    Background:
        Given the store operates on a channel named "Web-EU"
        And there is a customer "Lucy" identified by an email "lucy@teamlucifer.com" and a password "pswd"
        And this customer has placed an order "#00000001" on a channel "Web-EU"
        And there is a customer "Satin" identified by an email "satin@teamlucifer.com" and a password "pswd"
        And this customer has placed an order "#00000002" on a channel "Web-EU"
        And I am logged in as an administrator

    @ui
    Scenario: Export all customers
        When I want to see all customers in store
        And I want to export customers
        Then I should download a csv file with 2 customers
        And the csv file should contains "satin@teamlucifer.com"

    @ui @javascript
    Scenario: Export selected customers
        When I want to see all customers in store
        And I check the "satin@teamlucifer.com" customer
        And I want to export customers
        Then I should download a csv file with 1 customers
        And the csv file should contains "satin@teamlucifer.com"
        And the csv file should not contains "lucy@teamlucifer.com"
