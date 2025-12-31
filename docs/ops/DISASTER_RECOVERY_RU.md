# Disaster Recovery (3.x LTS)

## Scenarios
- App loss: redeploy signed artifact + restore configs/storage.
- DB loss: restore from dump/snapshot + validate migrations.
- Bad upgrade: rollback (files + DB snapshot) + clear caches.

## Probes
- /api/health
- /api/ready
