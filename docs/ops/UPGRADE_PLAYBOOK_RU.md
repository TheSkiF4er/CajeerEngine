# Upgrade Playbook (3.x LTS)

## Steps
1. Read release notes.
2. Backup DB + storage + configs.
3. Upgrade on staging.
4. Run smoke tests (health/ready, CRUD, workers, marketplace).
5. Canary rollout in production.
6. Rollback if needed.
