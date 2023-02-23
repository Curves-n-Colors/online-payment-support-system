describe("Login Admin", () => {
    it("Enters Dashboard.", () => {
        cy.login({ email: "info@thundercodes.com" });

        cy.currentUser().its("email").should("eq", "info@thundercodes.com");

        cy.currentUser().then((user) => {
            expect(user.email).to.eql("info@thundercodes.com");
        });
        cy.visit("/clients");
        cy.get(".no-margin > .btn").click();
        cy.get(":nth-child(2) > .controls > .form-control").type('Test User');
        cy.get(":nth-child(3) > .controls > .form-control").type('test@test.com');
        cy.get(":nth-child(4) > .controls > .form-control").type('9841235689');
        cy.contains("button", "CREATE CLIENT").click();
         cy.assertRedirect("/clients");
    });
});
