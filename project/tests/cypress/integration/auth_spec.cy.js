describe('Authentication', () => {
    it('sign a user in', ()=> {
        cy.refreshDatabase();
        cy.seed();
        cy.visit('/login');
        cy.get(":nth-child(2) > .controls > .form-control").type(
            "info@thundercodes.com"
        );
        cy.get(":nth-child(3) > .controls > .form-control").type('123456789');
        cy.contains("button","Sign in").click();
        cy.assertRedirect("/dashboard");
    });
});