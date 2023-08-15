describe("Create Payment", () => {
    it("Generate a payment", () => {
        cy.login({ email: "info@thundercodes.com" });

        cy.currentUser().its("email").should("eq", "info@thundercodes.com");

        // cy.currentUser().then((user) => {
        //     expect(user.email).to.eql("info@thundercodes.com");
        // });
        cy.visit("/payment/setups");
        cy.get(".no-margin > .btn").click();
        cy.get(":nth-child(2) > .controls > .form-control").type('New test Pyament');
        cy.get(".select2-selection__rendered").click();
        cy.get(".select2-results__option").click();
        cy.get(".select-recurrence").select(2);
        cy.get(":nth-child(5) > .form-group > .controls > .form-control")
            .click()
            .type("2023-10-14");
        cy.get(".col-md-8 > .form-group > .form-input-group > .form-control").type('Test Title');
        cy.get(".autonumeric").type('10');
        cy.contains("CREATE PAYMENT SETUP").click();
        cy.assertRedirect("/payment/setups");        
    });
});
